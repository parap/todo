<?php

use Classes\Db;
use Classes\Routing;
use Classes\RequestM;
use Classes\AuthHandler;

require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/vendor/autoload.php';

session_start();

$db = new Db($user, $password, $host, $db);
$conn = $db->getConnection();

$request = RequestM::createFromGlobals();
$routing = new Routing($request, $routes);
$route   = $routing->getRoute();

$controller = $routing->getControllerName();
$action     = $routing->getActionName();
// TODO: move it to routing?
try {
    $auth = new AuthHandler($freeRoutes);
    $auth->verify($route, $request);
} catch (Exception $e) {
    $controller = 'Controllers\UserController';
    $action = 'showLogout';
}

echo (new $controller($conn))->$action($request);
