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

class ImageController extends BaseController {

    public function editPostAction(Application $app, Request $request) {

        $imageId = $request->get('id');

        $image = array(
            'title' => 'Pussy-distroyer',
            'private' => 'false',
            'id' => $imageId,
            'src' => "../../assets/images/test.JPG",
            'editable' => 'true'
        );

        $user = $app['session']->get('user');

        $content = $app['twig']->render('post.twig', array(
            'page' => 'Edit post',
            'navs' => parent::createNavLinks(SitePage::MyProfile),
            'brandText' => 'PWGram',
            'brandSrc' => '../../assets/images/brand.png',
            'image' => $image,
            'user' => $user,
            'count' => 2
        ));

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');
        $response->setContent($content);

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
            'comments' => $comments
        );

        $user = $app['session']->get('user');

        $content = $app['twig']->render('post.twig', array(
            'page' => 'Post details',
            'navs' => parent::createNavLinks(SitePage::MyProfile),
            'brandText' => 'PWGram',
            'brandSrc' => '../../assets/images/brand.png',
            'image' => $image,
            'user' => $user,
            'count' => 2
        ));

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');
        $response->setContent($content);

        return $response;
    }
}