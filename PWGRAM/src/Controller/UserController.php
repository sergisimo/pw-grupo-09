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
            'navs' => parent::createNavLinks(SitePage::MyProfile, $app),
            'brandText' => parent::brandText($app),
            'brandSrc' => parent::brandImage($app, SitePage::MyProfile),
            'user' => $user
        ));

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');

        if ($app['sessionActive']) $response->setContent($content);
        else $response->setContent(parent::deniedContent($app, 'You must be authenticated in order to view your profile', SitePage::MyProfile));

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
                'src' => '../assets/images/test2.png',
                'title' => 'Els fotògrafs',
                'postPage' => '/post/view/1'
            ),
            array(
                'src' => '../assets/images/test3.png',
                'title' => 'SalleFeeest',
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
            'navs' => parent::createNavLinks(SitePage::MyProfile, $app),
            'brandText' => parent::brandText($app),
            'brandSrc' => parent::brandImage($app, SitePage::SecondLevel),
            'user' => $user
        ));

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');
        $response->setContent($content);

        return $response;
    }

    public function myPostsAction(Application $app) {

        $posts = array(
            array(
                'src' => '../assets/images/test.JPG',
                'title' => 'Pussy distroyer',
                'postPage' => '/post/edit/1',
                'id' => '1'
            ),
            array(
                'src' => '../assets/images/test2.png',
                'title' => 'Els fotògrafs',
                'postPage' => '/post/edit/1',
                'id' => '2'
            ),
            array(
                'src' => '../assets/images/test3.png',
                'title' => 'SalleFeeeest',
                'postPage' => '/post/edit/1',
                'id' => '3'
            )
        );

        $content = $app['twig']->render('myPosts.twig', array(
            'app' => [
                'name' => $app['app.name']
            ],
            'page' => 'My posts',
            'posts' => $posts,
            'navs' => parent::createNavLinks(SitePage::MyPosts, $app),
            'brandText' => parent::brandText($app),
            'brandSrc' => parent::brandImage($app, SitePage::MyPosts)
        ));

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');

        if ($app['sessionActive']) $response->setContent($content);
        else $response->setContent(parent::deniedContent($app, 'You must be authenticated in order to view your posts', SitePage::MyPosts));

        return $response;
    }

    public function myCommentsAction(Application $app) {

        $comments = array(
            array(
                'imageSrc' => 'assets/images/test_thubnail.JPG',
                'content' => 'Pff quin home més sexy!!!',
                'id' => 0
            ),
            array(
                'imageSrc' => 'assets/images/test2_thubnail.png',
                'content' => 'Menys aula natura i més php',
                'id' => 1
            )
        );

        $content = $app['twig']->render('myComments.twig', array(
            'app' => [
                'name' => $app['app.name']
            ],
            'page' => 'My comments',
            'navs' => parent::createNavLinks(SitePage::MyComments, $app),
            'brandText' => parent::brandText($app),
            'brandSrc' => parent::brandImage($app, SitePage::MyComments),
            'comments' => $comments
        ));

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');

        if ($app['sessionActive']) $response->setContent($content);
        else $response->setContent(parent::deniedContent($app, 'You must be authenticated in order to view your comments', SitePage::MyComments));

        return $response;
    }

    public function notificationsAction(Application $app) {

        $content = $app['twig']->render('notifications.twig', array(
            'app' => [
                'name' => $app['app.name']
            ],
            'page' => 'Notifications',
            'navs' => parent::createNavLinks(SitePage::Notifications, $app),
            'brandText' => parent::brandText($app),
            'brandSrc' => parent::brandImage($app, SitePage::Notifications)
        ));

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');

        if ($app['sessionActive']) $response->setContent($content);
        else $response->setContent(parent::deniedContent($app, 'You must be authenticated in order to view your notifications', SitePage::Notifications));

        return $response;
    }
}