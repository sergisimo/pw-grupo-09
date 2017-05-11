<?php
/**
 * Created by PhpStorm.
 * User: Esteve Genovart
 * Date: 29/03/2017
 * Time: 19:14
 */

ini_set('display_errors', 1);
require_once __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../app/app.php';
$app['debug'] = true;
require __DIR__.'/../app/config/prod.php';
require __DIR__.'/../app/config/routes.php';
$app->run();
