<?php

namespace Classes;

class DbAssist
{
    protected function query($query)
    {
        $res = mysqli_query($this->conn, $query) or die(mysqli_error());

        if (!$res) {
            return [];
        }

        if (true === $res) {
            return true;
        }

        $result = [];
        while ($row = mysqli_fetch_assoc($res)) {
            $result[] = $row;
        }

        return $result;
    }
    protected function safe($arg)
    {
        $arg = str_replace('\\"', '"', $arg);

        return mysqli_real_escape_string($this->conn, $arg);
    }
}