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

        $username = $request->getM('username') ? : $request->postM['username'];

        if (!empty($_SESSION['logged-email']) &&
                $_SESSION['logged-email'] === $username) {
            return;
        }

        throw new \Exception('Wrong name');
    }

}
