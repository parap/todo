<?php

namespace Classes;

class UserRepo extends DbAssist {
    public function login($name, $password)
    {
        $name = $this->safe($name);
        $password = $this->safe($password);
        
        if (!$this->nameExists($name)) {
            return false;
        }
        
        $query = "SELECT id, salt, password FROM user WHERE email = '%s'";
        $res = $this->query(sprintf($query, $name))[0];
        
        $pswToCheck = hash('sha512', sprintf('%s%s', $password, $res['salt']));
        
        return $res['password'] === $pswToCheck;
                
    }
    
    public function register($name, $password)
    {
        $name = $this->safe($name);
        $password = $this->safe($password);
        $salt = 'nams' . rand(0, 10000);
        $pswd = hash('sha512', sprintf('%s%s', $password, $salt));
        
        if ($this->nameExists($name)) {
            return false;
        }
        
        $query = sprintf("INSERT INTO user (email, password, salt, created_at) "
                . " VALUES ('%s', '%s', '%s', NOW());", $name, $pswd, $salt);
        
        $this->query($query);
    }
    
    public function nameExists($name)
    {
        $name = $this->safe($name);
        $query = "SELECT id FROM user WHERE email = '%s'";
        $res = $this->query(sprintf($query, $name));
        
        return (boolean)$res;
    }
}