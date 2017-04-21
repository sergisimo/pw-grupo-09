<?php
/**
 * Created by PhpStorm.
 * User: Esteve Genovart
 * Date: 29/03/2017
 * Time: 19:10
 */

$app->get('/hello/{name}','SilexApp\\Controller\\HelloController::indexAction');
$app->get('/add/{num1}/{num2}','SilexApp\\Controller\\HelloController::addAction');

$app->get('/users/get/{id}','SilexApp\\Controller\\UserController::getAction');
$app->get('/users/add/','SilexApp\\Controller\\UserController::postAction');



