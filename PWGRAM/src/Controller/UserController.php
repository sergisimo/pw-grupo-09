<?php
/**
 * Created by PhpStorm.
 * User: borjaperez
 * Date: 11/5/17
 * Time: 16:37
 */

namespace SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\SitePage;

class UserController extends BaseController {

    public function myProfileAction(Application $app) {

        $content = $app['twig']->render('404.twig', array(
            'app' => [
                'name' => $app['app.name']
            ],
            'page' => '404 not found',
            'navs' => parent::createNavLinks(SitePage::NotFound),
            'brandText' => 'PWGram',
            'brandSrc' => 'assets/images/brand.png',
            'title' => 'Access denied',
            'message' => 'You must log in before accessing to your profile page'
        ));

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');
        $response->setContent($content);

        return $response;
    }
}