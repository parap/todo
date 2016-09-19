<?php

namespace Classes;

use Classes\ItemType;

class ItemRepo extends DbAssist
{
    public function fetch($date)
    {
        $query = "SELECT *, DATEDIFF(NOW(), completed_at) as delay "
                . "FROM item WHERE created_at <= '$date'";

        $results = $this->query($query);
        $doneDailys = $this->fetchCompletedDaily($date);
        
        foreach($results as $key => $one) {
            if ('1' === $one['type']) {
                $results[$key]['done'] = in_array($one['id'], $doneDailys) ? '1' : '0';
            } 
        }
        
        return $results;
    }
    
    public function fetchCompletedDaily($date)
    {
        $template = 'SELECT item_id FROM daily WHERE completed_at = "%s"';
        $query = sprintf($template, $date);
        
        return array_map(function($x){return $x['item_id'];}, $this->query($query));
    }

    public function create($name, $userId, $parentId, $type)
    {
        $query = sprintf(
            'INSERT INTO item (name, user_id, done, parent_id, type, created_at, todo_at, completed_at) '
                . 'VALUES ("%s", "%s", "0", "%s", "%s", NOW(), NOW(), "0000-00-00")',
            $this->safe($name),
            $userId,
            $parentId,
            $type
        );

        return $this->query($query);
    }

    public function update($id, $text)
    {
        $id = $this->safe($id);
        $text = $this->safe($text);
        $query = "UPDATE item SET name='$text' WHERE id='$id'";

        return $this->query($query);
    }

    public function complete($id, $type, $date)
    {
        if ($type === ItemType::Daily) {
            $this->completeDaily($id, $date);
        }
        
        $id = $this->safe($id);
        $query = "UPDATE item SET done='1', completed_at='$date' WHERE id='$id'";

        return $this->query($query);
    }
    
    public function completeDaily($id, $date) {
        $query = sprintf(
                "INSERT INTO daily (item_id, completed_at) VALUES ('%s', '%s')", 
                $this->safe($id),
                $date
        );

        return $this->query($query);
    }
    
    public function findDelay($id)
    {
        $id = $this->safe($id);
        $query = "SELECT DATEDIFF(NOW(), completed_at) as delay "
                . "FROM item WHERE id='$id'";

        return $this->query($query);
    }

    public function findLatestCompletedAt($id)
    {
        $query = "SELECT completed_at "
                . "FROM daily "
                . "WHERE item_id='$id' "
                . "ORDER BY completed_at DESC "
                . "LIMIT 1";
        $result = $this->query($query);
        
        return isset($result[0]['completed_at']) ? $result[0]['completed_at'] : '';
    }

    public function uncomplete($id, $type, $date)
    {
        $latest = '';
        $id = $this->safe($id);
        
        if ($type === ItemType::Daily) {
            $this->uncompleteDaily($id, $date);
            $latest = $this->findLatestCompletedAt($id);
        }
        
        $completedAt = $latest ?: '0000-00-00';
        
        $query = "UPDATE item SET done='0', completed_at='$completedAt' WHERE id='$id'";

        return $this->query($query);
    }
    
    public function uncompleteDaily($id, $date)
    {
        $query = sprintf(
                "DELETE FROM daily WHERE item_id='%s' AND completed_at = '%s'", 
                $this->safe($id),
                $date
                );
        
        return $this->query($query);
    }

    public function remove($id)
    {
        $id = $this->safe($id);
        $query = "DELETE from item where id='$id'";
        $queryDaily = "DELETE from daily WHERE item_id='$id'";

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
        $timestamp = mktime(date('H'), date('i'), date('s'), date('m'), date('d') - date('w') +1, date('Y'));
        
        return date($format, $timestamp);
    }

        public function fetchStatistic()
    {        
            $res = array_merge_recursive($this->totalStatistic('2015-01-01', 'all'),
                $this->totalStatistic($this->getLastMondayDate(), 'week'),
                $this->totalStatistic(date('Y-m' . '-01'), 'month')
                );
        
        return $res;
        // 
// total & monthly & weekly completion % of every daily task separately;
// total & monthly & weekly completion % of all daily/normal tasks;
    }
    
    public function dailySingleStatistic()
    {
        $res = array_merge_recursive($this->dailySingleFor('2015-01-01', 'all'),
                $this->dailySingleFor($this->getLastMondayDate(), 'week'),
                $this->dailySingleFor(date('Y-m' . '-01'), 'month')
                );
        
        return $res;
                
    }
    
    public function dailySingleFor($date, $index)
    {
        $query = "SELECT i.name , COUNT( d.completed_at ) AS number_completion, DATEDIFF(NOW(), i.created_at) AS life_length
FROM item i
LEFT JOIN daily d ON ( i.id = d.item_id ) 
WHERE d.completed_at >=  '$date' AND i.type = '1'
GROUP BY i.id";
        
        $got = $this->query($query);
        $results = [];
        foreach($got as $one) {
            $results[$one['name']][$index] = $one;
        }
        
        return $results;
    }

    public function totalStatistic($date, $index)
    {
        $query = "SELECT COUNT(completed_at) as total_done FROM daily WHERE completed_at >= '$date'";
        $totalCompleted = $this->query($query)[0]['total_done'];
        
        $query = "SELECT SUM(DATEDIFF(NOW(), i.created_at)) AS life_length_after
FROM item i
LEFT JOIN daily d ON ( i.id = d.item_id ) 
WHERE i.created_at >=  '$date' AND i.type = '1'";    
        $totalCreatedAfter = $this->query($query)[0]['life_length_after'];
        
        $query = "SELECT COUNT(i.id) as total
FROM item i
WHERE i.created_at < '$date' AND i.type = '1'";
        $items = $this->query($query)[0]['total'];
        
        $query = "SELECT DATEDIFF(NOW(), '$date') + 1 as diff";
        $days = $this->query($query)[0]['diff'];
        
        $totalCreatedBefore = $items * $days;
        
        return [$index => ['done' => (int)$totalCompleted, 'time' => (int)$totalCreatedAfter + (int)$totalCreatedBefore]];
    }
}
