<?php

namespace Classes;

class Db
{
    public function __construct($user, $password, $host, $db)
    {
        mysql_connect($host, $user, $password);
        mysql_select_db($db);
    }
}
