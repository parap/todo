<?php

namespace Classes;

use Classes\ItemType;

class ItemRepo extends DbAssist
{

    public function fetch($date, $email)
    {
        $emailP = $this->safe($email);

        // subquery included in DATEDIFF 
        // seeks the most recent item completion date in the past 
        // to create correct delay
        
        $query = "SELECT i.*, "
                . "GROUP_CONCAT(rr.number) as numbers, "
                . "rr.interval_type AS interval_type, "
                . "DATEDIFF(i.todo_at, NOW()) AS time_left, "
                . "DATEDIFF(NOW(), (SELECT d.completed_at FROM completed AS d "
                . "WHERE d.item_id = i.id "
                . "AND d.completed_at <= NOW() "
                . "ORDER BY completed_at DESC "
                . "LIMIT 1)) AS delay "
                . "FROM item i "
                . "LEFT JOIN user u ON i.user_id = u.id "
                . "LEFT JOIN repeats r ON i.id = r.item_id "
                . "LEFT JOIN repeats rr ON i.id = rr.item_id "
                . "WHERE i.created_at <= '$date' AND "
                . "(i.archived_at = '0000-00-00' OR i.archived_at > '$date') "
                . "AND u.email = '$emailP' "
                . "AND (i.type = '0' OR i.type = '1' "
                . "OR (i.type = '2' AND WEEKDAY('$date') = r.number) "
                . "OR (i.type = '3' AND DAYOFMONTH('$date') = r.number) ) "
                . "GROUP BY i.id "
                ;
        
//        echo $query;

        $results    = $this->query($query);
        $doneDailys = $this->fetchCompletedDaily($date);

        foreach ($results as $key => $one) {
            if ('1' === $one['type']) {
                $results[$key]['done'] = in_array($one['id'], $doneDailys) ? '1' : '0';
            }
        }

        return $results;
    }

    public function fetchArchived($date, $email)
    {
        $emailP = $this->safe($email);      
        $query = "SELECT i.* "
                . "FROM item i "
                . "LEFT JOIN user u ON i.user_id = u.id "                
                . "WHERE i.created_at <= '$date' AND i.archived_at > '0000-00-00' "
                . "AND i.archived_at <= '$date' AND u.email='$emailP'";

        $results    = $this->query($query);
        $doneDailys = $this->fetchCompletedDaily($date);

        foreach ($results as $key => $one) {
            if ('1' !== $one['type']) {
                continue;
            }

            $results[$key]['done'] = in_array($one['id'], $doneDailys) ? '1' : '0';
        }

        return $results;
    }

    public function fetchCompletedDaily($date)
    {
        $template = 'SELECT item_id FROM completed '
                . 'WHERE completed_at = "%s"'
//                . 'AND completed_at <= NOW()'
                ;
        $query    = sprintf($template, $date);

        return array_map(function($x) {
            return $x['item_id'];
        }, $this->query($query));
    }

    public function getUserIdByEmail($email)
    {
        $query = "SELECT id FROM user WHERE email = '$email'";
        return $this->query($query)[0]['id'];
    }

    public function create($name, $email, $parentId, $type, $params)
    {
        $userId = $this->getUserIdByEmail($email);
        $query  = sprintf(
                'INSERT INTO item (name, user_id, done, parent_id, type, '
                . 'created_at, todo_at, completed_at, archived_at) '
                . 'VALUES ("%s", "%s", "0", "%s", "%s", NOW(), NOW(), '
                . '"0000-00-00", "0000-00-00")', $this->safe($name), $userId, $parentId, $type
        );
        
        $this->query($query);

        if (in_array($type, [ItemType::Normal, ItemType::Daily])) {
            return;
        }

        if(!empty($params['day'])) {
            return;
            // plain completed task, do nothing
        }
        
        $this->query("SELECT @last := LAST_INSERT_ID()");
        
        if (!empty($params['month'])) {
            $this->createRepeats($params['month'], 'm');
            return;
        }
        
        if (empty($params['week'])) {
            return; 
        }
        
        foreach ($params['week'] as $key => $value) {
            if (!$value) {
                continue;
            }

            $this->createRepeats(--$key, 'w');
        }
    }

    public function createRepeats($number, $intervalType)
    {
        $number       = $this->safe($number);
        $intervalType = $this->safe($intervalType);
        $query        = 'INSERT INTO repeats (item_id, number, interval_type, '
                . 'created_at, archived_at, repeatt) '
                . 'VALUES (@last, "%s", "%s", NOW(), "0000-00-00", "1")';
        $this->query(sprintf($query, $number, $intervalType));
    }

    public function update($id, $text)
    {
        $id    = $this->safe($id);
        $text  = $this->safe($text);
        $query = "UPDATE item SET name='$text' WHERE id='$id'";

        return $this->query($query);
    }

    public function complete($id, $type, $date)
    {
        if ($type === ItemType::Daily) {
            $this->completeDaily($id, $date);
        }

        $id    = $this->safe($id);
        
        $query = "UPDATE item SET done='1', completed_at='$date' WHERE id='$id'";

        return $this->query($query);
    }

    public function completeDaily($id, $date)
    {
        $query = sprintf(
                "INSERT INTO completed (item_id, completed_at) VALUES ('%s', '%s')", $this->safe($id), $date
        );

        return $this->query($query);
    }

    public function findDelay($id)
    {
        $id    = $this->safe($id);
        $query = "SELECT DATEDIFF(NOW(), (SELECT d.completed_at FROM completed AS d "
                . "WHERE d.item_id = i.id "
                . "AND d.completed_at <= NOW()"
                . "ORDER BY completed_at DESC "
                . "LIMIT 1)) AS delay "
                . "FROM item AS i "
                . "WHERE i.id = '$id'";

        return $this->query($query);
    }

    public function findLatestCompletedAt($id)
    {
        $query  = "SELECT completed_at "
                . "FROM completed "
                . "WHERE item_id='$id'"
                . "ORDER BY completed_at DESC "
                . "LIMIT 1";
        $result = $this->query($query);

        return isset($result[0]['completed_at']) ? $result[0]['completed_at'] : '';
    }

    public function uncomplete($id, $type, $date)
    {
        $latest = '';
        $id     = $this->safe($id);

        if ($type === ItemType::Daily) {
            $this->uncompleteDaily($id, $date);
            $latest = $this->findLatestCompletedAt($id);
        }

        $completedAt = $latest ? : '0000-00-00';

        $query = "UPDATE item SET done='0', completed_at='$completedAt' WHERE id='$id'";

        return $this->query($query);
    }

    public function uncompleteDaily($id, $date)
    {
        $query = sprintf(
                "DELETE FROM completed WHERE item_id='%s' AND completed_at = '%s'", $this->safe($id), $date
        );

        return $this->query($query);
    }

    public function archive($id)
    {
        $id    = $this->safe($id);
        $query = "UPDATE item SET archived_at=NOW() WHERE id='$id'";

        return $this->query($query);
    }

    public function unarchive($id)
    {
        $id    = $this->safe($id);
        $query = "UPDATE item SET archived_at='0000-00-00' WHERE id='$id'";

        return $this->query($query);
    }

    public function remove($id)
    {
        $id         = $this->safe($id);
        $query      = "DELETE from item where id='$id'";
        $queryDaily = "DELETE from completed WHERE item_id='$id'";
        $queryRepeats = "DELETE from repeats WHERE item_id='$id'";

        $this->query($queryDaily);
        $this->query($queryRepeats);
        return $this->query($query);
    }

    /**
     * @TODO: move it to other class - Datetime?
     * 
     * @param type $format
     * @return type
     */
    public function getLastMondayDate($format = 'Y-m-d')
    {
        $days      = date('w') ? date('d') - date('w') + 1 : date('d') - date('w') - 6;
        $timestamp = mktime(date('H'), date('i'), date('s'), date('m'), $days, date('Y'));

        return date($format, $timestamp);
    }

    /**
     * @brief fetches statistic
     * 
     * @return array
     */
    public function fetchStatistic($email)
    {
        $res = array_merge_recursive(
                $this->totalStatistic('2015-01-01', 'all', $email), 
                $this->totalStatistic($this->getLastMondayDate(), 'week', $email), 
                $this->totalStatistic(date('Y-m' . '-01'), 'month', $email)
        );

        return $res;
        // 
// total & monthly & weekly completion % of every completed task separately;
// total & monthly & weekly completion % of all daily/normal tasks;
    }

    public function dailySingleStatistic($email)
    {
        $res = array_merge_recursive(
                $this->dailySingleFor('2015-01-01', 'all', $email), 
                $this->dailySingleFor($this->getLastMondayDate(), 'week', $email), 
                $this->dailySingleFor(date('Y-m' . '-01'), 'month', $email)
        );

        return $res;
    }

    public function dailySingleFor($date, $index, $email)
    {
        $emailP = $this->safe($email);
        $query = "SELECT i.name , COUNT( d.completed_at ) AS number_completion, DATEDIFF(NOW(), i.created_at)+1 AS life_length
            FROM item i
LEFT JOIN completed d ON ( i.id = d.item_id ) 
LEFT JOIN user u ON u.id = i.user_id 
WHERE (d.completed_at >=  '$date' OR d.completed_at IS NULL) "
                . "AND i.type = '1' "
                . "AND (i.archived_at = '0000-00-00' OR i.archived_at >= '$date') "
                . "AND u.email = '$emailP' "
                . "GROUP BY i.id";

        $got     = $this->query($query);
        $results = [];
        foreach ($got as $one) {
            $results[$one['name']][$index] = $one;
        }

        return $results;
    }

    public function totalStatistic($date, $index, $email)
    {
        $emailP = $this->safe($email);
        $query          = "SELECT COUNT(d.completed_at) as total_done "
                . "FROM completed d "
                . "LEFT JOIN item i ON i.id = d.item_id "
                . "LEFT JOIN user u ON u.id = i.user_id "
                . "WHERE d.completed_at >= '$date' "
                . "AND u.email='$emailP'";
        $totalCompleted = $this->query($query)[0]['total_done'];

        $query = "SELECT SUM(DATEDIFF(NOW(), i.created_at)) AS life_length_after
FROM item i
LEFT JOIN completed d ON ( i.id = d.item_id ) 
LEFT JOIN user u ON u.id = i.user_id 
WHERE i.created_at >=  '$date' AND i.type = '1' AND u.email='$emailP'";
        $totalCreatedAfter = $this->query($query)[0]['life_length_after'];

        $query = "SELECT COUNT(i.id) as total
FROM item i
LEFT JOIN user u ON u.id = i.user_id 
WHERE i.created_at <= '$date' AND i.type = '1' AND u.email='$emailP'";
        $items = $this->query($query)[0]['total'];

        $query = "SELECT DATEDIFF(NOW(), '$date') + 1 as diff";
        $days  = $this->query($query)[0]['diff'];

        $totalCreatedBefore = $items * $days;

        return [$index => ['done' => (int) $totalCompleted, 'time' => (int) $totalCreatedAfter + (int) $totalCreatedBefore]];
    }

    public function setDate($id, $date)
    {
        $id    = $this->safe($id);
        $query = "UPDATE item SET todo_at='$date' WHERE id='$id'";

        return $this->query($query);
    }
    
    public function findTimeLeft($id)
    {
        $id    = $this->safe($id);
        $query = "SELECT DATEDIFF(i.todo_at, NOW()) AS time_left, done "
                . "FROM item AS i "
                . "WHERE i.id = '$id' ";
        return $this->query($query)[0];
    }
}
