<?php
/**
 * Created by PhpStorm.
 * User: Esteve Genovart
 * Date: 29/03/2017
 * Time: 19:10
 */

use Silex\Application;

$session = array(
    'active' => true,
    'user' => array(
        'notifications' => array(
            'count' => 2
        )
    )
);

$app = new Application();
$app['app.name'] = 'SilexApp';
$app['session'] = $session;

return $app;