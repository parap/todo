<?php

use Classes\Db;
use Classes\Routing;
use Classes\RequestM;
use Classes\AuthHandler;
use Controllers\UserController;

require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/vendor/autoload.php';

session_start();

new Db($user, $password, $host, $db);

$request = RequestM::createFromGlobals();
$routing = new Routing($request, $routes);
$route   = $routing->getRoute();

// TODO: move it to routing?
try {
    $auth = new AuthHandler($freeRoutes);
    $auth->verify($route, $request);
} catch (Exception $e) {
//    echo (new UserController)->showLogout($request);
//    return;
}

$controller = $routing->getControllerName();
$action     = $routing->getActionName();

echo (new $controller)->$action($request);
