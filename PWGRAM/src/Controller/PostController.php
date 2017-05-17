<?php
/**
 * Created by PhpStorm.
 * User: borjaperez
 * Date: 12/5/17
 * Time: 14:49
 */

namespace SilexApp\Controller;

use Silex\Application;
use SilexApp\Model\Comment;
use SilexApp\Model\DAOComment;
use SilexApp\Model\DAOImage;
use SilexApp\Model\DAOLike;
use SilexApp\Model\DAONotification;
use SilexApp\Model\DAOUser;
use SilexApp\Model\Image;
use SilexApp\Model\Like;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\SitePage;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class PostController extends BaseController {

    public function editPostAction(Application $app, Request $request) {

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');

        if ($app['session']->get('id') == null) {
            $response->setContent(parent::deniedContent($app, 'You must be authenticated in order to edit your posts', SitePage::ThirdLevel));
            return $response;
        }

        $imageId = $request->get('id');

        $image = DAOImage::getInstance()->getImage($imageId);

        if ($image == null) {
            $response->setContent(parent::deniedContent($app, 'The image you are trying to edit does not exist', SitePage::ThirdLevel));
            return $response;
        }

        $imageAux = array(
            'title' => $image->getTitle(),
            'private' => $image->getPrivate(),
            'id' => $imageId,
            'src' => '../../' . $image->getImgPath(),
            'editable' => 'true'
        );

        $user = $app['session']->get('user');

        $count = 0;
        if ($user != null) $count = count(DAONotification::getInstance()->getNotificationsByUser($user->getId()));

        $content = $app['twig']->render('post.twig', array(
            'page' => 'Edit post',
            'navs' => parent::createNavLinks(SitePage::MyProfile, $app),
            'brandText' => parent::brandText($app),
            'brandSrc' => parent::brandImage($app, SitePage::ThirdLevel),
            'image' => $imageAux,
            'user' => $user,
            'count' => $count
        ));

        if ($image->getUserId() != $app['session']->get('id')) $response->setContent(parent::deniedContent($app, 'The image you are trying to edit is not yours', SitePage::ThirdLevel));
        else $response->setContent($content);

        return $response;
    }

    public function viewPostAction(Application $app, Request $request) {

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');

        $imageId = $request->get('id');
        $image = DAOImage::getInstance()->getImage($imageId);
        if ($image == null) {
            $response->setContent(parent::deniedContent($app, 'The image you are trying to edit does not exist', SitePage::ThirdLevel));
            return $response;
        }
        $image->setVisits($image->getVisits() + 1);
        DAOImage::getInstance()->updateImageVisits($image);

        $user = $app['session']->get('user');
        $active = false;
        if ($app['session']->get('id') != null) $active = true;

        $comments = DAOComment::getInstance()->getCommentByImageID($imageId);
        $commentsInfo = array();
        $userCanComment = true;

        foreach ($comments as $comment) {

            array_push($commentsInfo, array(
                'username' => DAOUser::getInstance()->getUserById($comment->getUserId())->getUsername(),
                'content' => $comment->getText()
            ));

            if ($user != null && DAOUser::getInstance()->getUserById($comment->getUserId())->getUsername() == $user->getUsername()) $userCanComment = false;
        }


        $private = false;
        if ($image->getPrivate() == 1) $private = true;
        $liked = false;
        if ($user != null) $liked = DAOLike::getInstance()->checkIsLiked($user->getId(), $imageId);

        $imageInfo = array(
            'title' => $image->getTitle(),
            'private' => $private,
            'id' => $imageId,
            'src' => '../../' . $image->getImgPath(),
            'editable' => false,
            'days' => $image->calculateDays(),
            'likes' => count(DAOLike::getInstance()->getLikeByImageID($imageId)),
            'visits' => $image->getVisits(),
            'username' => DAOUser::getInstance()->getUserById($image->getUserId())->getUsername(),
            'comments' => $commentsInfo,
            'userProfile' => '/profile/' . $image->getUserId(),
            'liked' => $liked,
            'userCanComment' => $userCanComment
        );

        $count = 0;
        if ($user != null) $count = count(DAONotification::getInstance()->getNotificationsByUser($user->getId()));

        $content = $app['twig']->render('post.twig', array(
            'page' => 'Post details',
            'navs' => parent::createNavLinks(SitePage::MyProfile, $app),
            'brandText' => parent::brandText($app),
            'brandSrc' => parent::brandImage($app, SitePage::ThirdLevel),
            'image' => $imageInfo,
            'sessionActive' => $active,
            'user' => $user,
            'count' => $count
        ));

        if ($private == false) $response->setContent($content);
        else $response->setContent(parent::deniedContent($app, 'Only public posts can be viewed', SitePage::ThirdLevel));

        return $response;
    }

    public function addPostAction(Application $app) {

        $user = $app['session']->get('user');

        $count = 0;
        if ($user != null) $count = count(DAONotification::getInstance()->getNotificationsByUser($user->getId()));

        $content = $app['twig']->render('post.twig', array(
            'page' => 'Add post',
            'navs' => parent::createNavLinks(SitePage::AddPost, $app),
            'brandText' => parent::brandText($app),
            'brandSrc' => parent::brandImage($app, SitePage::AddPost),
            'user' => $user,
            'count' => $count
        ));

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');

        if ($app['session']->get('id') != null) $response->setContent($content);
        else $response->setContent(parent::deniedContent($app, 'You must be authenticated in order to view your posts', SitePage::AddPost));

        return $response;
    }

    public function uploadImageAction() {

        $sourcePath = $_FILES['file']['tmp_name'];
        $targetPath = "assets/images/posts/" . $_FILES['file']['name'];
        move_uploaded_file($sourcePath, $targetPath) ;

        $extension = explode('.', $_FILES['file']['name'])[1];

        $image = $this->resizeImage($targetPath, 400, 300, false, $extension);
        imagejpeg($image, $targetPath);

        $newTargetPath = "assets/images/postsIcon/" . $_FILES['file']['name'];

        $image = $this->resizeImage($targetPath, 100, 100, false, 'jpg');
        imagejpeg($image, $newTargetPath);

        return new JsonResponse();
    }

    public function uploadPostAction(Application $app) {

        $image = new Image();

        $image->setUserId($app['session']->get('id'));
        $image->setPrivate($_POST['private']);
        $image->setTitle($_POST['title']);
        $image->setImgPath('assets/images/posts/' . $_POST['imagePath']);

        DAOImage::getInstance()->insertImage($image);

        return new JsonResponse();
    }

    public function updatePostInfoAction(Application $app) {

        $image = DAOImage::getInstance()->getImage($_POST['id']);

        if (strlen($_POST['title']) > 0) $image->setTitle($_POST['title']);

        $image->setPrivate($_POST['private']);

        if ($_POST['imagePath'] != null) {
            $iconPath = explode('/', $image->getImgPath());

            unlink('assets/images/posts/' . $iconPath[3]);
            unlink('assets/images/postsIcon/' . $iconPath[3]);

            $image->setImgPath('assets/images/posts/' . $_POST['imagePath']);
        }

        DAOImage::getInstance()->updateImage($image);

        return new JsonResponse();
    }

    public function deletePostAction(Application $app) {

        $imageID = $_POST['id'];
        $image = DAOImage::getInstance()->getImage($imageID);

        $iconPath = explode('/', $image->getImgPath());

        unlink('assets/images/posts/' . $iconPath[3]);
        unlink('assets/images/postsIcon/' . $iconPath[3]);

        DAOImage::getInstance()->deleteImage($imageID);

        return new JsonResponse();
    }

    public function likePostAction(Application $app) {

        $like = new Like();

        $like->setImageId($_POST['imageID']);
        $like->setUserId($app['session']->get('id'));

        DAOLike::getInstance()->insertLike($like);

        return new JsonResponse();
    }

    public function unlikePostAction(Application $app) {

        $like = new Like();

        $like->setImageId($_POST['imageID']);
        $like->setUserId($app['session']->get('id'));

        DAOLike::getInstance()->deleteLike($like);

        return new JsonResponse();
    }

    public function commentPostAction(Application $app) {

        $comment = new Comment();

        $comment->setImageId($_POST['imageID']);
        $comment->setText($_POST['text']);
        $comment->setUserId($app['session']->get('id'));

        DAOComment::getInstance()->insertComment($comment);

        return new JsonResponse();
    }

    public function uncommentPostAction(Application $app) {

        $comment = new Comment();

        $comment->setImageId($_POST['imageID']);
        $comment->setUserId($app['session']->get('id'));

        DAOComment::getInstance()->deleteComment($comment);

        return new JsonResponse();
    }

    /* PRIVATE METHODS */

    private function resizeImage($file, $w, $h, $crop = FALSE, $fileType) {

        list($width, $height) = getimagesize($file);

        $r = $width / $height;

        $src = null;

        /*if ($crop) {
            if ($width > $height) $width = ceil($width - ($width * abs($r -$w / $h)));
            else $height = ceil($height - ($height * abs($r - $w / $h)));

            $newwidth = $w;
            $newheight = $h;
        }
        else {
            if ($w / $h > $r) {
                $newwidth = $h * $r;
                $newheight = $h;
            }
            else {
                $newheight = $w / $r;
                $newwidth = $w;
            }
        }*/

        switch ($fileType) {
            case 'png':
                $src = imagecreatefrompng($file);
                break;
            case 'jpg':
                $src = imagecreatefromjpeg($file);
                break;
            case 'gif':
                $src = imagecreatefromgif($file);
                break;
        }

        $dst = imagecreatetruecolor($w, $h);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, $width, $height);

        return $dst;
    }
}