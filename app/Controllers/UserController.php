<?php

namespace Controllers;

use Symfony\Component\HttpFoundation\Request;
use Classes\UserRepo;

class UserController 
{
    protected $repo;
    protected $post;
    protected $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->repo = new UserRepo($conn);
        $this->post = json_decode(file_get_contents('php://input'),true);
    }
    
    public function showLogout()
    {
        return 'logged out';
    }
    
    public function logout(Request $request)
    {
        unset($_COOKIE['logged-email']);
        setcookie('logged-email', '', time() - 3600);
    }

    public function login(Request $request)
    {
        $result = $this->repo->login($this->post['email'], $this->post['ps']);
        
        if ($result) {
            setcookie('logged-email', $this->post['email'], time() + 3600 * 24 * 30 * 2, '/');
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
