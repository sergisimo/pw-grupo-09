<?php
/**
 * Created by PhpStorm.
 * User: Esteve Genovart
 * Date: 03/04/2017
 * Time: 15:59
 */

namespace SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\SitePage;
use SilexApp\Model\User;

class HomeController extends BaseController {

    public function indexAction(Application $app) {

        $posts = array (
            'mostViewed' => array (
                array(
                    'src' => '../assets/images/test.JPG',
                    'title' => 'Pussy distroyer',
                    'postPage' => '/post/view/1',
                    'postDate' => '2017-05-08',
                    'userProfile' => '/profile/1',
                    'username' => 'bperezme',
                    'liked' => true,
                    'likes' => '1K',
                    'visits' => '69',
                    'lastComment' => array(
                        'username' => 'sanfe',
                        'content' => 'El terror de las nenas'
                    ),
                    'userCanComment' => true,
                    'id' => 1
                ),
                array(
                    'src' => '../assets/images/test2.png',
                    'title' => 'Els fotògrafs',
                    'postPage' => '/post/view/1',
                    'postDate' => '2017-04-22',
                    'userProfile' => '/profile/1',
                    'username' => 'bperezme',
                    'liked' => true,
                    'likes' => '102',
                    'visits' => '200',
                    'lastComment' => array(
                        'username' => 'bperezme',
                        'content' => 'Menys aula natura i més php'
                    ),
                    'userCanComment' => false,
                    'id' => 2
                ),
                array(
                    'src' => '../assets/images/test3.png',
                    'title' => 'SalleFeeeest',
                    'postPage' => '/post/view/1',
                    'postDate' => '2017-05-01',
                    'userProfile' => '/profile/1',
                    'username' => 'bperezme',
                    'liked' => false,
                    'likes' => '10',
                    'visits' => '23',
                    'userCanComment' => true,
                    'id' => 3
                )
            ),
            'mostRecent' => array (
                array(
                    'src' => '../assets/images/test2.png',
                    'title' => 'Els fotògrafs',
                    'postPage' => '/post/view/1',
                    'postDate' => '2017-04-22',
                    'userProfile' => '/profile/1',
                    'username' => 'bperezme',
                    'liked' => true,
                    'likes' => '102',
                    'visits' => '200',
                    'lastComment' => array(
                        'username' => 'bperezme',
                        'content' => 'Menys aula natura i més php'
                    ),
                    'userCanComment' => false,
                    'id' => 2
                ),
                array(
                    'src' => '../assets/images/test3.png',
                    'title' => 'SalleFeeeest',
                    'postPage' => '/post/view/1',
                    'postDate' => '2017-05-01',
                    'userProfile' => '/profile/1',
                    'username' => 'bperezme',
                    'liked' => false,
                    'likes' => '10',
                    'visits' => '23',
                    'userCanComment' => true,
                    'id' => 3
                ),
                array(
                    'src' => '../assets/images/test.JPG',
                    'title' => 'Pussy distroyer',
                    'postPage' => '/post/view/1',
                    'postDate' => '2017-05-08',
                    'userProfile' => '/profile/1',
                    'username' => 'bperezme',
                    'liked' => true,
                    'likes' => '1K',
                    'visits' => '69',
                    'lastComment' => array(
                        'username' => 'sanfe',
                        'content' => 'El terror de las nenas'
                    ),
                    'userCanComment' => true,
                    'id' => 1
                )
            )
        );

        $user = new User();
        $active = true;

        //$app['session']->set('id', null);
        if ($app['session']->get('id') == null) $active = false;
        else $user = $app['session']->get('user');

        $count = 0;
        if ($user != null) $count = count(DAONotification::getInstance()->getNotificationsByUser($user->getId()));

        $content = $app['twig']->render('home.twig', array(
            'page' => 'Home',
            'sectionTitle' => 'Last Posts',
            'navs' => parent::createNavLinks(SitePage::Home, $app),
            'brandText' => parent::brandText($app),
            'brandSrc' => parent::brandImage($app, SitePage::Home),
            'posts' => $posts,
            'active' => $active,
            'user' => $user,
            'count' => $count
        ));

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');
        $response->setContent($content);

        return $response;
    }
}