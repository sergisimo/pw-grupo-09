<?php
/**
 * Created by PhpStorm.
 * User: borjaperez
 * Date: 11/5/17
 * Time: 16:45
 */

namespace SilexApp\Controller;

use Silex\Application;
use SilexApp\Model\SitePage;

class BaseController {

    protected function createNavLinks($sourcePage, Application $app) {

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
                        'name' => 'My comments',
                        'href' => '/myComments',
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

                if ($app['session']->get('id') != null) {
                    unset($links['login']);
                    unset($links['register']);
                }
                else {
                    unset($links['addPost']);
                    unset($links['myComents']);
                    unset($links['myPosts']);
                    unset($links['notifications']);
                }

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
            case SitePage::MyProfile:
                unset($links['login']);
                unset($links['register']);

                if ($app['session']->get('id') == null) {
                    unset($links['addPost']);
                    unset($links['myPosts']);
                    unset($links['myComents']);
                    unset($links['notifications']);
                }

                break;
        }

        return $links;
    }

    protected function brandText(Application $app) {

        if ($app['session']->get('id') != null) return $app['session']->get('user')->getUsername();
        else return 'PWGram';
    }

    protected function brandImage(Application $app, $sourcePage) {

        $noSessionBrandSource = 'assets/images/brand.png';
        $sessionBrandSource = '';

        if ($sourcePage == SitePage::SecondLevel) {
            $noSessionBrandSource = '../assets/images/brand.png';
            $sessionBrandSource = '../';
        }
        else if ($sourcePage == SitePage::ThirdLevel) {
            $noSessionBrandSource = '../../assets/images/brand.png';
            $sessionBrandSource = '../../';
        }

        if ($app['session']->get('id') != null) {
            if ($app['session']->get('user')->getImgPath() == 'assets/images/defaultProfile.png') $sessionBrandSource = $sessionBrandSource . 'assets/images/defaultProfileBrand.png';
            else $sessionBrandSource = $sessionBrandSource . $app['session']->get('user')->getImgPath();

            return $sessionBrandSource;
        } else return $noSessionBrandSource;
    }

    protected function deniedContent(Application $app, $message, $sourcePage) {

        $content = $app['twig']->render('404.twig', array(
            'app' => [
                'name' => $app['app.name']
            ],
            'page' => 'Error 404',
            'navs' => $this->createNavLinks(SitePage::NotFound, $app),
            'brandText' => $this->brandText($app),
            'brandSrc' => $this->brandImage($app, $sourcePage),
            'message' => $message
        ));

        return $content;
    }
}