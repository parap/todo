<?php

namespace Classes;

class Db
{
    private $conn = null;

    public function __construct($user, $password, $host, $db)
    {
        $this->conn = mysqli_connect($host, $user, $password, $db);
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
