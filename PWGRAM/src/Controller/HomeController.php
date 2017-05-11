<?php
/**
 * Created by PhpStorm.
 * User: Esteve Genovart
 * Date: 03/04/2017
 * Time: 15:59
 */

namespace SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\SitePage;


class HomeController {

    private function createNavLinks($page) {

        $links = array();

        switch ($page) {
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
        }

        return $links;
    }

    public function indexAction(Application $app, Request $request) {

        //$name = $request->query->get('name');
        $content = $app['twig']->render('home.twig', array(
            'app' => [
                'name' => $app['app.name']
            ],
            'page' => 'Home',
            'sectionTitle' => 'Last Posts',
            'navs' => $this->createNavLinks(SitePage::Home),
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