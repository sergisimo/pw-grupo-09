<?php
/**
 * Created by PhpStorm.
 * User: borjaperez
 * Date: 11/5/17
 * Time: 16:37
 */

namespace SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\SitePage;

class UserController extends BaseController {

    public function myProfileAction(Application $app) {

        $content = $app['twig']->render('404.twig', array(
            'app' => [
                'name' => $app['app.name']
            ],
            'page' => 'Error 404',
            'navs' => parent::createNavLinks(SitePage::NotFound),
            'brandText' => 'PWGram',
            'brandSrc' => 'assets/images/brand.png',
            'message' => 'You must be authenticated in order to access to your profile page'
        ));

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');
        $response->setContent($content);

        return $response;
    }

    public function addPostAction(Application $app) {

        $content = $app['twig']->render('404.twig', array(
            'app' => [
                'name' => $app['app.name']
            ],
            'page' => 'Error 404',
            'navs' => parent::createNavLinks(SitePage::NotFound),
            'brandText' => 'PWGram',
            'brandSrc' => 'assets/images/brand.png',
            'message' => 'You must be authenticated in order to add a new post'
        ));

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');
        $response->setContent($content);

        return $response;
    }

    public function myPostsAction(Application $app) {

        /*$content = $app['twig']->render('404.twig', array(
            'app' => [
                'name' => $app['app.name']
            ],
            'page' => 'Error 404',
            'navs' => parent::createNavLinks(SitePage::NotFound),
            'brandText' => 'PWGram',
            'brandSrc' => 'assets/images/brand.png',
            'message' => 'You must be authenticated in order to view your posts'
        ));*/

        $content = $app['twig']->render('myPosts.twig', array(
            'app' => [
                'name' => $app['app.name']
            ],
            'page' => 'My posts',
            'navs' => parent::createNavLinks(SitePage::MyPosts),
            'brandText' => 'PWGram',
            'brandSrc' => 'assets/images/brand.png'
        ));

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');
        $response->setContent($content);

        return $response;
    }

    public function myCommentsAction(Application $app) {

        /*$content = $app['twig']->render('404.twig', array(
            'app' => [
                'name' => $app['app.name']
            ],
            'page' => 'Error 404',
            'navs' => parent::createNavLinks(SitePage::NotFound),
            'brandText' => 'PWGram',
            'brandSrc' => 'assets/images/brand.png',
            'message' => 'You must be authenticated in order to view your comments'
        ));*/

        $content = $app['twig']->render('myComments.twig', array(
            'app' => [
                'name' => $app['app.name']
            ],
            'page' => 'My comments',
            'navs' => parent::createNavLinks(SitePage::MyComments),
            'brandText' => 'PWGram',
            'brandSrc' => 'assets/images/brand.png'
        ));

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');
        $response->setContent($content);

        return $response;
    }

    public function notificationsAction(Application $app) {

        /*$content = $app['twig']->render('404.twig', array(
            'app' => [
                'name' => $app['app.name']
            ],
            'page' => 'Error 404',
            'navs' => parent::createNavLinks(SitePage::NotFound),
            'brandText' => 'PWGram',
            'brandSrc' => 'assets/images/brand.png',
            'message' => 'You must be authenticated in order to view your notifications'
        ));*/

        $content = $app['twig']->render('notifications.twig', array(
            'app' => [
                'name' => $app['app.name']
            ],
            'page' => 'Notifications',
            'navs' => parent::createNavLinks(SitePage::Notifications),
            'brandText' => 'PWGram',
            'brandSrc' => 'assets/images/brand.png'
        ));

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');
        $response->setContent($content);

        return $response;
    }
}