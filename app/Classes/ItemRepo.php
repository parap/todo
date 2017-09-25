<?php

namespace Classes;

use Classes\ItemType;

class ItemRepo extends DbAssist
{
    protected $conn;
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function fetch($date, $email)
    {
        $emailP = $this->safe($email);

        // subquery included in DATEDIFF 
        // seeks the most recent item completion date in the past 
        // to create correct delay

        $query = "SELECT i.*, "
                . "GROUP_CONCAT(DISTINCT rr.number ORDER BY rr.number ASC) as numbers, "
                . "GROUP_CONCAT(s.name ORDER BY s.id ASC SEPARATOR '###') as subitems, "
                . "(SELECT ss.name FROM subitem ss "
                . "WHERE ss.completed_at = '0000-00-00' "
                . "AND ss.item_id = i.id "
                . "ORDER BY ss.id ASC LIMIT 1) as next_subitem,"
                . "(SELECT ss.name FROM subitem ss "
                . "WHERE ss.completed_at <> '0000-00-00' "
                . "AND ss.item_id = i.id "
                . "ORDER BY ss.id DESC LIMIT 1) as last_completed_subitem,"
                . "rr.interval_type AS interval_type, "
                . "DATEDIFF(i.todo_at, NOW()) AS time_left, "
                . "DATEDIFF(NOW(), (SELECT d.completed_at FROM completed AS d "
                . "WHERE d.item_id = i.id "
                . "AND d.completed_at <= NOW() "
                . "ORDER BY d.completed_at DESC "
                . "LIMIT 1)) AS delay "
                . "FROM item i "
                . "LEFT JOIN user u ON i.user_id = u.id "
                . "LEFT JOIN repeats rr ON i.id = rr.item_id "
                . "LEFT JOIN subitem s ON i.id = s.item_id "
                . "WHERE i.created_at <= '$date' AND "
                . "(i.archived_at = '0000-00-00' OR i.archived_at > '$date') "
                . "AND u.email = '$emailP' "
                . "GROUP BY i.id "
                . "ORDER BY i.id ASC, s.id ASC "
        ;

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
        $query  = "SELECT i.* "
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

    public function create($name, $email, $type, $params)
    {
        $userId = $this->getUserIdByEmail($email);
        $query  = sprintf(
                'INSERT INTO item (name, user_id, done, type, '
                . 'created_at, todo_at, completed_at, archived_at) '
                . 'VALUES ("%s", "%s", "0", "%s", NOW(), NOW(), '
                . '"0000-00-00", "0000-00-00")', $this->safe($name), $userId, $type
        );

        $this->query($query);

        if (!empty($params['subtasks'])) {
            $last = $this->query("SELECT @last := LAST_INSERT_ID()");
            $last = $last[0]['@last := LAST_INSERT_ID()'];
            foreach ($params['subtasks'] as $key => $task) {
                $task = $this->safe($task);
                $this->createSubtask($last, $task);
            }

            return;
        }

        if (in_array($type, [ItemType::Normal, ItemType::Daily])) {
            return;
        }

        if (!empty($params['day'])) {
            return;
            // plain completed task, do nothing
        }

        $last = $this->query("SELECT @last := LAST_INSERT_ID()");
        $last = $last[0]['@last := LAST_INSERT_ID()'];

        if (!empty($params['month'])) {
            $this->createRepeats($params['month'], 'm', $last);
            return;
        }

        if (empty($params['week'])) {
            return;
        }

        foreach ($params['week'] as $key => $value) {
            if (!$value) {
                continue;
            }

            $this->createRepeats( --$key, 'w', $last);
        }
    }

    public function createSubtask($last, $name)
    {
        $query = 'INSERT INTO subitem (item_id, name) VALUES ("%s", "%s")';
        $this->query(sprintf($query, $last, $name));
    }

    /**
     * 
     * @param type $number
     * @param type $intervalType
     */
    public function createRepeats($number, $intervalType, $last)
    {
        $number       = $this->safe($number);
        $last         = $this->safe($last);
        $intervalType = $this->safe($intervalType);
        $query        = 'INSERT INTO repeats (item_id, number, interval_type, '
                . 'created_at, archived_at, repeatt) '
                . 'VALUES ("%s", "%s", "%s", NOW(), "0000-00-00", "1")';
        $this->query(sprintf($query, $last, $number, $intervalType));
    }

    public function removeItemFromRepeats($id)
    {
        $this->query("DELETE FROM repeats WHERE item_id = '$id'");
    }

    public function removeItemFromSubitem($id)
    {
        $this->query("DELETE FROM subitem WHERE item_id = '$id'");
    }

    public function update($id, $text, $type, $numbers, $sub)
    {
        $id   = $this->safe($id);
        $text = $this->safe($text);
        $type = $this->safe($type);

        $query = "UPDATE item SET name='$text', type='$type' WHERE id='$id'";
        $this->query($query);

        $this->removeItemFromSubitem($id);
        // revolver
        if (!empty($sub)) {
            foreach ($sub as $subitem) {
                $this->createSubtask($id, $this->safe($subitem));
            }

            return;
        }

        //1 daily 2 weekly 3 monthly
        $this->removeItemFromRepeats($id);

        if ('2' === $type) {
            for ($i = 0; $i < strlen($numbers); $i++) {
                $this->createRepeats($numbers[$i], 'w', $id);
            }
        } elseif ('3' === $type) {
            $this->createRepeats($numbers, 'm', $id);
        }

        return;
    }

    public function complete($id, $type, $date)
    {
        if ($type === ItemType::Daily || $type === ItemType::Weekly) {
            $this->completeDaily($id, $date);
        }
        
        $id = $this->safe($id);

        $query = "UPDATE item SET done='1', completed_at='$date' WHERE id='$id'";

        return $this->query($query);
    }

    public function completeNext($id, $date)
    {
        $id = $this->safe($id);

        $query = "UPDATE subitem 
            SET completed_at = NOW()
            WHERE completed_at = '0000-00-00'
            AND item_id = '$id'
            ORDER BY id ASC LIMIT 1";

        $this->query($query);

        // if there are no "next" subtask left - mark parent task as completed
        $next = $this->findNextSubtask($id);
        if (empty($next)) {
            $this->complete($id, ItemType::Normal, $date);
        }
    }

    public function uncompleteLast($id, $date)
    {
        $id = $this->safe($id);

        $query = "UPDATE subitem 
            SET completed_at = '0000-00-00'
            WHERE completed_at <> '0000-00-00' 
            AND item_id = '$id'
            ORDER BY id DESC LIMIT 1";

        $this->query($query);

        // if there are "next" subtask(s) left - mark parent task as uncompleted
        $next = $this->findNextSubtask($id);
        if (!empty($next)) {
            $this->uncomplete($id, ItemType::Normal, $date);
        }
    }

    public function findNextSubtask($id)
    {
        $id    = $this->safe($id);
        $query = "SELECT ss.name FROM subitem ss "
                . "WHERE ss.completed_at = '0000-00-00' "
                . "AND ss.item_id = '$id' "
                . "ORDER BY ss.id ASC LIMIT 1";
        return $this->query($query);
    }

    public function completeDaily($id, $date)
    {
        $template = "INSERT INTO completed (item_id, completed_at) "
                . "VALUES ('%s', '%s')";
        $query    = sprintf($template, $this->safe($id), $date);

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

        if ($type === ItemType::Daily || $type === ItemType::Weekly) {
            $this->uncompleteDaily($id, $date);
            $latest = $this->findLatestCompletedAt($id);
        }

        $completedAt = $latest ? : '0000-00-00';

        $query = "UPDATE item SET done='0', completed_at='$completedAt' "
                . "WHERE id='$id'";

        return $this->query($query);
    }

    public function uncompleteDaily($id, $date)
    {
        $template = "DELETE FROM completed WHERE item_id='%s' "
                . "AND completed_at = '%s'";
        $query    = sprintf($template, $this->safe($id), $date);

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
        $id = $this->safe($id);

        $this->removeItemFromRepeats($id);
        $this->removeItemFromSubitem($id);

        $query      = "DELETE from item where id='$id'";
        $queryDaily = "DELETE from completed WHERE item_id='$id'";

        $this->query($queryDaily);
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
        $query  = "SELECT i.name , COUNT( d.completed_at ) AS number_completion, DATEDIFF(NOW(), i.created_at)+1 AS life_length
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
        $emailP         = $this->safe($email);
        $query          = "SELECT COUNT(d.completed_at) as total_done "
                . "FROM completed d "
                . "LEFT JOIN item i ON i.id = d.item_id "
                . "LEFT JOIN user u ON u.id = i.user_id "
                . "WHERE d.completed_at >= '$date' "
                . "AND u.email='$emailP'";
        $totalCompleted = $this->query($query)[0]['total_done'];

        $query1            = "SELECT SUM(DATEDIFF(NOW(), i.created_at)) AS life_length_after
FROM item i
LEFT JOIN completed d ON ( i.id = d.item_id ) 
LEFT JOIN user u ON u.id = i.user_id 
WHERE i.created_at >=  '$date' AND i.type = '1' AND u.email='$emailP'";
        $totalCreatedAfter = $this->query($query1)[0]['life_length_after'];

        $query2 = "SELECT COUNT(i.id) as total
FROM item i
LEFT JOIN user u ON u.id = i.user_id 
WHERE i.created_at <= '$date' AND i.type = '1' AND u.email='$emailP'";
        $items  = $this->query($query2)[0]['total'];

        $query3 = "SELECT DATEDIFF(NOW(), '$date') + 1 as diff";
        $days   = $this->query($query3)[0]['diff'];

        $totalCreatedBefore = $items * $days;

        return [$index =>
            ['done' => (int) $totalCompleted,
                'time' => (int) $totalCreatedAfter + (int) $totalCreatedBefore]
        ];
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
