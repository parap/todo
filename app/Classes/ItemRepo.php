<?php

namespace Classes;

class ItemRepo extends DbAssist
{
    public function fetch()
    {
        $query = 'SELECT * FROM item';

        return $this->query($query);
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
        $query = "UPDATE item SET name='$name' WHERE id='$id'";

        return $this->query($query);
    }

    public function complete($id)
    {
        $id = $this->safe($id);
        $query = "UPDATE item SET done='1' WHERE id='$id'";

        return $this->query($query);
    }

    public function uncomplete($id)
    {
        $id = $this->safe($id);
        $query = "UPDATE item SET done='0' WHERE id='$id'";

        return $this->query($query);
    }

    public function remove($id)
    {
        $id = $this->safe($id);
        $query = "DELETE from message where id='$id'";

        return $this->query($query);
    }
}
