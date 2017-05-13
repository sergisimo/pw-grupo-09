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
use Symfony\Component\HttpFoundation\Request;

class UserController extends BaseController {

    public function myProfileAction(Application $app) {

        $user = array(
            'username' => 'bperezme',
            'birthdate' => '1995-10-03',
            'profileImage' => 'assets/images/defaultProfile.png',
            'selfUser' => 'true'
        );

        $content = $app['twig']->render('profile.twig', array(
            'app' => [
                'name' => $app['app.name']
            ],
            'page' => 'My profile',
            'navs' => parent::createNavLinks(SitePage::MyProfile),
            'brandText' => 'PWGram',
            'brandSrc' => 'assets/images/brand.png',
            'user' => $user
        ));

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');
        $response->setContent($content);

        return $response;
    }

    public function publicProfileAction(Application $app, Request $request) {

        $profileId = $request->get('id');

        $posts = array(
            array(
                'src' => '../assets/images/test.JPG',
                'title' => 'Pussy distroyer',
                'postPage' => '/post/view/1'
            ),
            array(
                'src' => '../assets/images/test.JPG',
                'title' => 'Pussy distroyer',
                'postPage' => '/post/view/1'
            ),
            array(
                'src' => '../assets/images/test.JPG',
                'title' => 'Pussy distroyer',
                'postPage' => '/post/view/1'
            )
        );

        $user = array(
            'username' => 'bperezme',
            'birthdate' => '1995-10-03',
            'profileImage' => '../assets/images/defaultProfile.png',
            'userId' => $profileId,
            'commentsAmount' => 1,
            'postsAmount' => 3,
            'posts' => $posts
        );

        $content = $app['twig']->render('profile.twig', array(
            'app' => [
                'name' => $app['app.name']
            ],
            'page' => $user['username'],
            'navs' => parent::createNavLinks(SitePage::MyProfile),
            'brandText' => 'PWGram',
            'brandSrc' => '../assets/images/brand.png',
            'user' => $user
        ));

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');
        $response->setContent($content);

        return $response;
    }

    public function myPostsAction(Application $app) {

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