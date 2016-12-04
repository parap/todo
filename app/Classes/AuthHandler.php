<?php

namespace Classes;

use Classes\RequestM;

class AuthHandler
{

    private $freeRoutes;

    public function __construct($freeRoutes)
    {
        $this->freeRoutes = $freeRoutes;
    }

    public function verify($route, RequestM $request)
    {
        if (in_array($route, $this->freeRoutes)) {
            return;
        }
        
//        throw new \Exception('Wrong name ');

        $username = $request->getM('email') ? : $request->postM['email'];

        if (!empty($_COOKIE['logged-email']) && $_COOKIE['logged-email'] === $username) {
            return;
        }

        //FIXME: use information-safe method instead of exception
        throw new \Exception('Wrong name ');
//        throw new \Exception('Wrong name '.$username.' and '.$_SESSION['logged-email']);
    }

}
