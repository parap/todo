<?php

namespace Classes;

class DbAssist
{
    protected function query($query)
    {
        $res = mysql_query($query) or die(mysql_error());

        if (!$res) {
            return [];
        }

        if (true === $res) {
            return true;
        }

        $result = [];
        while ($row = mysql_fetch_assoc($res)) {
            $result[] = $row;
        }

        return $result;
    }
    protected function safe($arg)
    {
        $arg = str_replace('\\"', '"', $arg);

        return mysql_real_escape_string($arg);
    }
}