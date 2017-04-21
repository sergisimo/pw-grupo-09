<?php
/**
 * Created by PhpStorm.
 * User: Esteve Genovart
 * Date: 19/04/2017
 * Time: 19:27
 */

namespace SilexApp\Providers;


use Pimple\Container;
use Pimple\ServiceProviderInterface;

class HelloServiceProvider implements ServiceProviderInterface {

    public function register(Container $app) {
        // TODO: Implement register() method.
        $app['hello'] = $app->protect(function ($name) use ($app) {
            $default = $app['hello.default_name'] ? $app['hello.default_name'] : '';
            $name = $name ?: $default;

            return $app['twig']->render('hello.twig', array(
                'user' => $name,
                'app' => [
                    'name' => $app['app.name']
                ]
            ));
        });
    }
}