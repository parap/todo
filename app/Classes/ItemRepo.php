<?php

namespace Classes;

use Classes\ItemType;

class ItemRepo extends DbAssist
{
    public function fetch($date)
    {
        $query = 'SELECT * FROM item';

        $results = $this->query($query);
        $doneDailys = $this->fetchCompletedDaily($date);
        
        foreach($results as $key => $one) {
            if (in_array($one['id'], $doneDailys)) {
                $results[$key]['done'] = '1';
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
                . 'VALUES ("%s", "%s", "0", "%s", "%s", NOW(), NOW(), NOW())',
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

    public function complete($id, $type)
    {
        if ($type === ItemType::Daily) {
            return $this->completeDaily($id);
        }
        
        $id = $this->safe($id);
        $query = "UPDATE item SET done='1' WHERE id='$id'";

        return $this->query($query);
    }
    
    public function completeDaily($id) {
        $query = sprintf(
                "INSERT INTO daily (item_id, completed_at) VALUES ('%s', NOW())", 
                $this->safe($id)
        );

        return $this->query($query);
    }

    public function uncomplete($id, $type)
    {
        if ($type === ItemType::Daily) {
            return $this->uncompleteDaily($id);
        }
        
        $id = $this->safe($id);
        $query = "UPDATE item SET done='0' WHERE id='$id'";

        return $this->query($query);
    }
    
    public function uncompleteDaily($id)
    {
        $query = sprintf(
                "DELETE FROM daily WHERE item_id='%s' AND completed_at = '%s'", 
                $this->safe($id),
                (new \DateTime)->format('Y-m-d')
                );
        
        return $this->query($query);
    }

    public function remove($id)
    {
        $id = $this->safe($id);
        $query = "DELETE from item where id='$id'";

        return $this->query($query);
    }
}
