<?php
/**
 * Created by PhpStorm.
 * User: borjaperez
 * Date: 11/5/17
 * Time: 16:45
 */

namespace SilexApp\Controller;

use SilexApp\Model\SitePage;

class BaseController {

    protected function createNavLinks($sourcePage) {

        $links = [  'home' => array(
                        'name' => 'Home',
                        'href' => '/',
                        'currentPage' => 0
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
                    ),
                    'addPost' => array(
                        'name' => 'Add post',
                        'href' => '/addPost',
                        'currentPage' => 0
                    ),
                    'myPosts' => array(
                        'name' => 'My posts',
                        'href' => '/myPosts',
                        'currentPage' => 0
                    ),
                    'myComents' => array(
                        'name' => 'My coments',
                        'href' => '/myComents',
                        'currentPage' => 0
                    ),
                    'notifications' => array(
                        'name' => 'Notifications',
                        'href' => '/notifications',
                        'currentPage' => 0
                    )
                ];

        switch ($sourcePage) {
            case SitePage::Home:
                $links['home']['currentPage'] = 1;
                break;
            case SitePage::NotFound:
                unset($links['login']);
                unset($links['register']);
                unset($links['addPost']);
                unset($links['myComents']);
                unset($links['myPosts']);
                unset($links['notifications']);
                break;
            case SitePage::AddPost:
                $links['addPost']['currentPage'] = 1;
                unset($links['login']);
                unset($links['register']);
                break;
            case SitePage::MyPosts:
                $links['myPosts']['currentPage'] = 1;
                unset($links['login']);
                unset($links['register']);
                break;
            case SitePage::MyComments:
                $links['myComents']['currentPage'] = 1;
                unset($links['login']);
                unset($links['register']);
                break;
            case SitePage::Notifications:
                $links['notifications']['currentPage'] = 1;
                unset($links['login']);
                unset($links['register']);
                break;
        }

        return $links;
    }
}