<?php
/**
 * Created by PhpStorm.
 * User: Esteve Genovart
 * Date: 29/03/2017
 * Time: 19:10
 */

    use Silex\Application;
    $app = new Application();
    $app['app.name'] = 'SilexApp';
    return $app;