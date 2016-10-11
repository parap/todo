<?php

namespace Controllers;

use Symfony\Component\HttpFoundation\Request;
use Classes\UserRepo;

class UserController 
{
    protected $repo;
    protected $post;

    public function __construct() 
    {
        $this->repo = new UserRepo();
        $this->post = json_decode(file_get_contents('php://input'),true);
    }

    public function login(Request $request)
    {
        $x = $this->repo->register('hello1', 'kitty');
        
        $result = $this->repo->login('nams', 'pswd');
        
        return $result;
    }
    
    public function register(Request $request)
    {
        $result = $this->repo->register($this->post('email'), $this->post('ps'));
        
        return $result;
    }
}