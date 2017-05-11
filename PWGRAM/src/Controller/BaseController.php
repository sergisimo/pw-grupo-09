<?php
/**
 * Created by PhpStorm.
 * User: borjaperez
 * Date: 11/5/17
 * Time: 16:45
 */

namespace SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\SitePage;

class BaseController {

    protected function createNavLinks($sourcePage) {

        $links = array();

        switch ($sourcePage) {
            case SitePage::Home:
                $links = [  'home' => array(
                                'name' => 'Home',
                                'href' => '/',
                                'currentPage' => 1
                            ),
                            'login' => array(
                                'name' => 'Login',
                                'href' => '',
                                'currentPage' => 0,
                                'dataTarget' => '#login'
                            ),
                            'register' => array(
                                'name' => 'Register',
                                'href' => '',
                                'currentPage' => 0,
                                'dataTarget' => '#register'
                            )
                        ];
                break;
            case SitePage::NotFound:
                $links = [  'home' => array(
                                'name' => 'Home',
                                'href' => '/',
                                'currentPage' => 0
                            )
                ];
                break;
        }

        return $links;
    }
}