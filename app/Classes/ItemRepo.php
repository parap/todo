<?php

namespace Classes;

class ItemRepo extends DbAssist
{
    public function fetch($approved = false)
    {
        $query = 'select * from message';

        if ($approved) {
            $query.=' where approved = 1';
        }

        $query.= ' ORDER BY added DESC';

        return $this->query($query);
    }

    public function create($name, $email, $text, $picture)
    {
        $query = sprintf(
            'INSERT INTO message (name, email, text, picture, added) values ("%s", "%s", "%s", "%s", NOW())',
            $this->safe($name),
            $this->safe($email),
            $this->safe($text),
            $picture
        );

        return $this->query($query);
    }

    public function update($id, $text)
    {
        $id = $this->safe($id);
        $text = $this->safe($text);
        $query = "UPDATE message SET text='$text', modified='1' WHERE id='$id'";

        return $this->query($query);
    }

    public function complete($id)
    {
        $id = $this->safe($id);
        $query = "UPDATE message SET approved='1' WHERE id='$id'";

        return $this->query($query);
    }

    public function uncomplete($id)
    {
        $id = $this->safe($id);
        $query = "UPDATE message SET approved='2' WHERE id='$id'";

        return $this->query($query);
    }

    public function remove($id)
    {
        $id = $this->safe($id);
        $query = "SELECT 1 from message where id='$id'";

        return $this->query($query);
    }
}
