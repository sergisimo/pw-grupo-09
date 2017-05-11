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

class HomeController extends BaseController {

    public function indexAction(Application $app) {

        $content = $app['twig']->render('home.twig', array(
            'app' => [
                'name' => $app['app.name']
            ],
            'page' => 'Home',
            'sectionTitle' => 'Last Posts',
            'navs' => parent::createNavLinks(SitePage::Home),
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