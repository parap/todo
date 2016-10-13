<?php

use Classes\Db;
use Classes\Routing;
use Classes\RequestM;

require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/vendor/autoload.php';

session_start();

new Db($user, $password, $host, $db);

$routing = new Routing($routes);
$controller = $routing->getControllerName();
$action = $routing->getActionName();
$request = RequestM::createFromGlobals();
echo (new $controller)->$action($request);
