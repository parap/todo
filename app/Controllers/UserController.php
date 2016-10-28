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
    
    public function showLogout()
    {
        //FIXME: use correct HTML redirect
        echo '<html><head><meta redirect /login></head></html>';
    }
    
    public function logout(Request $request)
    {
        unset($_SESSION['logged-email']);
    }

    public function login(Request $request)
    {
        $result = $this->repo->login($this->post['email'], $this->post['ps']);
        
        if ($result) {
            $_SESSION['logged-email'] = $this->post['email'];
        }
        
        $params = ['success'=> false, 'message'=> 'wrong login'];
        
        return $result ? json_encode(['success'=> true]) : json_encode($params);
    }
    
    public function register(Request $request)
    {
        $result = $this->repo->register($this->post['email'], $this->post['password']);
        
        $params = ['success'=> false, 'message'=> 'User exists or other error'];
        return $result ? json_encode(['success'=> true]) : json_encode($params);
    }
}