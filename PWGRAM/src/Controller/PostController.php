<?php
/**
 * Created by PhpStorm.
 * User: borjaperez
 * Date: 12/5/17
 * Time: 14:49
 */

namespace SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\SitePage;
use Symfony\Component\HttpFoundation\Request;

class PostController extends BaseController {

    public function editPostAction(Application $app, Request $request) {

        $imageId = $request->get('id');

        $image = array(
            'title' => 'Pussy-distroyer',
            'private' => 'false',
            'id' => $imageId,
            'src' => "../../assets/images/test.JPG",
            'editable' => 'true'
        );

        $content = $app['twig']->render('post.twig', array(
            'app' => [
                'name' => $app['app.name']
            ],
            'page' => 'Edit post',
            'navs' => parent::createNavLinks(SitePage::MyProfile, $app),
            'brandText' => parent::brandText($app),
            'brandSrc' => parent::brandImage($app, SitePage::ThirdLevel),
            'image' => $image
        ));

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');

        if ($app['sessionActive']) $response->setContent($content);
        else $response->setContent(parent::deniedContent($app, 'You must be authenticated in order to edit your posts', SitePage::ThirdLevel));

        return $response;
    }

    public function viewPostAction(Application $app, Request $request) {

        $imageId = $request->get('id');

        $comments = array(
            array(
                'username' => 'bperezme',
                'content' => 'Pff quin home més sexy!!!'
            ),
            array(
                'username' => 'sanfe',
                'content' => 'El terror de las nenas'
            )
        );

        $image = array(
            'title' => 'Pussy distroyer',
            'private' => 'false',
            'id' => $imageId,
            'src' => "../../assets/images/test.JPG",
            'editable' => 'false',
            'days' => '3',
            'likes' => '1K',
            'visits' => '69',
            'username' => 'bperezme',
            'comments' => $comments,
            'userProfile' => '/profile/1'
        );

        $content = $app['twig']->render('post.twig', array(
            'app' => [
                'name' => $app['app.name']
            ],
            'page' => 'Post details',
            'navs' => parent::createNavLinks(SitePage::MyProfile, $app),
            'brandText' => parent::brandText($app),
            'brandSrc' => parent::brandImage($app, SitePage::ThirdLevel),
            'image' => $image
        ));

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');

        if ($app['sessionActive'] || $image['private'] == 'false') $response->setContent($content);
        else $response->setContent(parent::deniedContent($app, 'You must be authenticated in order to view private posts', SitePage::ThirdLevel));

        return $response;
    }

    public function addPostAction(Application $app) {

        /*$content = $app['twig']->render('404.twig', array(
            'app' => [
                'name' => $app['app.name']
            ],
            'page' => 'Error 403',
            'navs' => parent::createNavLinks(SitePage::NotFound),
            'brandText' => 'PWGram',
            'brandSrc' => 'assets/images/brand.png',
            'message' => 'You must be authenticated in order to add a new post'
        ));*/

        $content = $app['twig']->render('post.twig', array(
            'app' => [
                'name' => $app['app.name']
            ],
            'page' => 'Add post',
            'navs' => parent::createNavLinks(SitePage::AddPost, $app),
            'brandText' => parent::brandText($app),
            'brandSrc' => parent::brandImage($app, SitePage::AddPost)
        ));

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');

        if ($app['sessionActive']) $response->setContent($content);

        if ($app['sessionActive']) $response->setContent($content);
        else $response->setContent(parent::deniedContent($app, 'You must be authenticated in order to add a post'), SitePage::AddPost);

        return $response;
    }
}