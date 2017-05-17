<?php
/**
 * Created by PhpStorm.
 * User: Esteve Genovart
 * Date: 03/04/2017
 * Time: 15:59
 */

namespace SilexApp\Controller;

use Silex\Application;
use SilexApp\Model\DAOComment;
use SilexApp\Model\DAOImage;
use SilexApp\Model\DAOLike;
use SilexApp\Model\DAOUser;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\SitePage;
use SilexApp\Model\User;
use SilexApp\Model\DAONotification;

class HomeController extends BaseController {

    public function indexAction(Application $app) {

        $user = null;
        $active = true;

        if ($app['session']->get('id') == null) $active = false;
        else $user = $app['session']->get('user');

        $images = DAOImage::getInstance()->getImageByOrder(true);
        $mostViewed = array();

        foreach ($images as $image) {

            $liked = false;
            if ($user != null && DAOLike::getInstance()->checkIsLiked($user->getId(), $image->getId())) $liked = true;

            $canComment = false;
            if ($user != null && DAOComment::getInstance()->checkCanCommnet($user->getId(), $image->getId())) $canComment = true;

            array_push($mostViewed, array(
                'src' => '../' . $image->getImgPath(),
                'title' => $image->getTitle(),
                'postPage' => '/post/view/' . $image->getId(),
                'postDate' => date("Y-m-d", strtotime($image->getCreatedAt())),
                'userProfile' => '/profile/' . $image->getUserId(),
                'username' => DAOUser::getInstance()->getUserById($image->getUserId())->getUsername(),
                'liked' => $liked,
                'likes' => count(DAOLike::getInstance()->getLikeByImageID($image->getId())),
                'visits' => $image->getVisits(),
                'lastComment' => null,
                'userCanComment' => $canComment,
                'id' => $image->getId()
            ));
        }


        $images = DAOImage::getInstance()->getImageByOrder(false);
        $mostRecent = array();

        foreach ($images as $image) {

            $liked = false;
            if ($user != null && DAOLike::getInstance()->checkIsLiked($user->getId(), $image->getId())) $liked = true;

            $canComment = false;
            if ($user != null && DAOComment::getInstance()->checkCanCommnet($user->getId(), $image->getId())) $canComment = true;

            $lastComment = null;
            if (count(DAOComment::getInstance()->getCommentByImageID($image->getId())) != 0) {
                $comment = DAOComment::getInstance()->getCommentByImageID($image->getId())[0];
                $lastComment = array(
                    'username' => DAOUser::getInstance()->getUserById($comment->getUserId())->getUsername(),
                    'content' => $comment->getText()
                );
            }

            array_push($mostRecent, array(
                'src' => '../' . $image->getImgPath(),
                'title' => $image->getTitle(),
                'postPage' => '/post/view/' . $image->getId(),
                'postDate' => date("Y-m-d", strtotime($image->getCreatedAt())),
                'userProfile' => '/profile/' . $image->getUserId(),
                'username' => DAOUser::getInstance()->getUserById($image->getUserId())->getUsername(),
                'liked' => $liked,
                'likes' => count(DAOLike::getInstance()->getLikeByImageID($image->getId())),
                'visits' => $image->getVisits(),
                'lastComment' => $lastComment,
                'userCanComment' => $canComment,
                'id' => $image->getId()
            ));
        }

        $posts = array(
            'mostViewed' => $mostViewed,
            'mostRecent' => $mostRecent
        );

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