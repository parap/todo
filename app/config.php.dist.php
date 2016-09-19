<?php

$routes = [
'fetch' => 'IndexController::fetch',
'create' => 'IndexController::create',
'update' => 'IndexController::update',
'complete' => 'IndexController::complete',
'remove' => 'IndexController::remove',
'archive' => 'IndexController::archive',
'unarchive' => 'IndexController::unarchive',
'fetch-archived' => 'IndexController::fetchArchived',
'statistic' => 'StatisticController::index',
'' => 'IndexController::fetch',
];

$user = 'user';
$password = 'password';
$host = 'localhost';
$db = 'db';