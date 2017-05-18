<?php
/**
 * Created by PhpStorm.
 * User: borjaperez
 * Date: 11/5/17
 * Time: 16:37
 */

namespace SilexApp\Controller;

use PHPMailer;

use Silex\Application;
use SilexApp\Model\DAOImage;
use SilexApp\Model\DAOLike;
use SilexApp\Model\Image;
use SilexApp\Model\LoginErrorCode;
use SilexApp\Model\Notification;
use SilexApp\Model\RegistrationErrorCode;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\SitePage;
use Symfony\Component\HttpFoundation\Request;
use SilexApp\Model\User;
use SilexApp\Model\DAOUser;
use SilexApp\Model\DAONotification;
use SilexApp\Model\DAOComment;

class UserController extends BaseController {

    public function registerAction() {

        $user = new User();
        $user->setUsername($_POST['username']);
        $user->setBirthdate($_POST['birthdate']);
        $user->setPassword($_POST['password']);
        $user->setEmail($_POST['email']);
        $user->setActive(0);

        $user->setImgPath('assets/images/profileImages/' . $_POST['imageName']);

        $errors = $user->validateRegistration();

        if (count($errors) == 0) {
            $user->setPassword(hash('sha512', $_POST['password']));
            DAOUser::getInstance()->insertUser($user);
            $user = DAOUser::getInstance()->getUser($user->getUsername());
            array_push($errors, RegistrationErrorCode::ErrorCodeRegistrationSuccesful);

            $this->sendRegistrationEmail($user->getEmail(), $user->getId());
        }

        $response = new JsonResponse($errors);

        return $response;
    }

    public function updateUserAction(Application $app) {

        $userID = $app['session']->get('id');

        if (isset($_POST['username']) && isset($_POST['birthdate']) && isset($_POST['password'])) {
            $user = DAOUser::getInstance()->getUserById($userID);

            if (strlen($_POST['username']) > 0) $user->setUsername($_POST['username']);

            if (strlen($_POST['birthdate']) > 0) $user->setBirthdate($_POST['birthdate']);

            if (strlen($_POST['password']) > 0) $user->setPassword(hash('sha512', $_POST['password']));

            if ($_POST['imageName'] != null) {
                if ($user->getImgPath() != 'assets/images/defaultProfile.png') unlink($user->getImgPath());
                $user->setImgPath('assets/images/profileImages/' . $_POST['imageName']);
            }

            DAOUser::getInstance()->updateUser($user);

            $app['session']->set('user', $user);

            return new JsonResponse();
        }
    }

    public function uploadImageAction() {

        $errors = array();
        array_push($errors, RegistrationErrorCode::ErrorCodeRegistrationSuccesful);

        $sourcePath = $_FILES['file']['tmp_name'];
        $targetPath = "assets/images/profileImages/" . $_FILES['file']['name'];
        move_uploaded_file($sourcePath, $targetPath) ;

        $response = new JsonResponse($errors);

        return $response;
    }

    public function validateAccountAction(Application $app, Request $request) {

        $id = $request->get('id');

        $user = DAOUser::getInstance()->getUserById($id);
        $user->setActive(1);

        DAOUser::getInstance()->updateUser($user);

        $app['session']->set('id', $user->getId());
        $app['session']->set('user', $user);

        $response = new RedirectResponse('/');
        return $response;
    }

    public function loginAction(Application $app) {

        $errors = array();

        $user = DAOUser::getInstance()->getUser($_POST['username']);

        if ($user == null) array_push($errors, LoginErrorCode::ErrorCodeNotFound);
        else if (!$user->validatePassword($_POST['password'])) array_push($errors, LoginErrorCode::ErrorCodeNotFound);
        else if (!$user->getActive()) array_push($errors, LoginErrorCode::ErrorCodeNotConfirmed);
        else {
            $app['session']->set('id', $user->getId());
            $app['session']->set('user', $user);
            array_push($errors, LoginErrorCode::ErrorCodeLoginSuccessful);
        }

        $response = new JsonResponse($errors);

        return $response;
    }

    public function myProfileAction(Application $app) {

        $userInfo = $app['session']->get('user');

        $user = array(
            'username' => $userInfo->getUsername(),
            'birthdate' => $userInfo->getBirthDate(),
            'profileImage' => $userInfo->getImgPath(),
            'selfUser' => 'true'
        );

        $count = 0;
        if ($userInfo != null) $count = count(DAONotification::getInstance()->getNotificationsByUser($userInfo->getId()));

        $content = $app['twig']->render('myProfile.twig', array(
            'page' => 'My profile',
            'navs' => parent::createNavLinks(SitePage::MyProfile, $app),
            'brandText' => parent::brandText($app),
            'brandSrc' => parent::brandImage($app, SitePage::MyProfile),
            'user' => $user,
            'count' => $count
        ));

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');

        if ($app['session']->get('id') != null) $response->setContent($content);
        else $response->setContent(parent::deniedContent($app, 'You must be authenticated in order to view your profile', SitePage::MyProfile));

        return $response;
    }

    public function publicProfileAction(Application $app, Request $request) {

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');

        $profileId = $request->get('id');
        $order = $request->get('order');
        $userInfo = DAOUser::getInstance()->getUserById($profileId);

        if ($userInfo == null) {
            $response->setContent(parent::deniedContent($app, 'The user you are trying to view does not exist', SitePage::MyProfile));
            return $response;
        }


        $images = DAOImage::getInstance()->getImagesByUserPublicID($profileId);
        $imagesInfo = array();

        foreach ($images as $image) {

            array_push($imagesInfo,
                array(
                    'src' => '../../' . $image->getImgPath(),
                    'title' => $image->getTitle(),
                    'postPage' => '/post/view/' . $image->getId(),
                    'date' => $image->getCreatedAt(),
                    'visits' => $image->getVisits(),
                    'numLikes' => count(DAOLike::getInstance()->getLikeByImageID($image->getId())),
                    'numComments' => count(DAOComment::getInstance()->getCommentByImageID($image->getId()))
                )
            );
        }

        if ($order == 0) usort($imagesInfo, array($this, 'compareLikes'));
        else if ($order == 1) usort($imagesInfo, array($this, 'compareComments'));
        else if ($order == 2) usort($imagesInfo, array($this, 'compareVisits'));
        else usort($imagesInfo, array($this, 'compareDates'));

        $count = 0;
        if ($userInfo != null) $count = count(DAONotification::getInstance()->getNotificationsByUser($userInfo->getId()));

        $user = array(
            'username' => $userInfo->getUsername(),
            'birthdate' => $userInfo->getBirthDate(),
            'profileImage' => $userInfo->getImgPath(),
            'userId' => $userInfo->getId(),
            'commentsAmount' => count(DAOComment::getInstance()->getCommentByUserID($profileId)),
            'postsAmount' => count(DAOImage::getInstance()->getImagesByUserID($profileId)),
            'posts' => $imagesInfo
        );

        $content = $app['twig']->render('profile.twig', array(
            'page' => $user['username'],
            'navs' => parent::createNavLinks(SitePage::MyProfile, $app),
            'brandText' => parent::brandText($app),
            'brandSrc' => parent::brandImage($app, SitePage::ThirdLevel),
            'user' => $user,
            'count' => $count
        ));

        $response->setContent($content);

        return $response;
    }

    public function myPostsAction(Application $app) {

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');

        if($app['session']->get('id') == null) {
            $response->setContent(parent::deniedContent($app, 'You must be authenticated in order to view your posts', SitePage::MyPosts));
            return $response;
        }

        $user = $app['session']->get('user');

        $posts = DAOImage::getInstance()->getImagesByUserID($app['session']->get('id'));
        $postsInfo = array();
        foreach ($posts as $post) {

            array_push($postsInfo, array(
                'src' => '../' . $post->getImgPath(),
                'title' => $post->getTitle(),
                'postPage' => '/post/edit/' . $post->getId(),
                'id' => $post->getId()
            ));
        }


        $count = 0;
        if ($user != null) $count = count(DAONotification::getInstance()->getNotificationsByUser($user->getId()));

        $content = $app['twig']->render('myPosts.twig', array(
            'page' => 'My posts',
            'posts' => $postsInfo,
            'navs' => parent::createNavLinks(SitePage::MyPosts, $app),
            'brandText' => parent::brandText($app),
            'brandSrc' => parent::brandImage($app, SitePage::MyPosts),
            'user' => $user,
            'count' => $count
        ));

        $response->setContent($content);

        return $response;
    }

    public function myCommentsAction(Application $app) {

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');

        if ($app['session']->get('id') == null) {
            $response->setContent(parent::deniedContent($app, 'You must be authenticated in order to view your comments', SitePage::MyComments));
            return $response;
        }

        $user = $app['session']->get('user');

        $comments = DAOComment::getInstance()->getCommentByUserID($user->getId());
        $commentsInfo = array();

        foreach ($comments as $comment) {

            array_push($commentsInfo, array(
                'imageSrc' => Image::transformImagePath(DAOImage::getInstance()->getImage($comment->getImageId())->getImgPath()),
                'content' => strip_tags($comment->getText()),
                'id' => $comment->getId()
            ));
        }

        $count = 0;
        if ($user != null) $count = count(DAONotification::getInstance()->getNotificationsByUser($user->getId()));

        $content = $app['twig']->render('myComments.twig', array(
            'page' => 'My comments',
            'navs' => parent::createNavLinks(SitePage::MyComments, $app),
            'brandText' => parent::brandText($app),
            'brandSrc' => parent::brandImage($app, SitePage::MyComments),
            'comments' => $commentsInfo,
            'user' => $user,
            'count' => $count
        ));

        $response->setContent($content);

        return $response;
    }

    public function notificationsAction(Application $app) {

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');

        if ($app['session']->get('id') == null) {
            $response->setContent(parent::deniedContent($app, 'You must be authenticated in order to view your comments', SitePage::Notifications));
            return $response;
        }

        $user = $app['session']->get('user');

        $notifications = DAONotification::getInstance()->getNotificationsByUser($user->getId());
        $notificationsInfo = array();

        foreach ($notifications as $notification) {

            array_push($notificationsInfo, array(
                'id' => $notification->getId(),
                'imageSrc' => Image::transformImagePath(DAOImage::getInstance()->getImage($notification->getImageId())->getImgPath()),
                'comment' => strip_tags($notification->getText()),
                'postName' => DAOImage::getInstance()->getImage($notification->getImageId())->getTitle(),
                'postPage' => '/post/view/' . DAOImage::getInstance()->getImage($notification->getImageId())->getId(),
                'sourceUserProfile' => 'profile/' . $notification->getUserId() . '/3',
                'sourceUsername' => DAOUser::getInstance()->getUserById($notification->getUserId())->getUsername()
            ));
        }

        $count = 0;
        if ($user != null) $count = count(DAONotification::getInstance()->getNotificationsByUser($user->getId()));

        $content = $app['twig']->render('notifications.twig', array(

            'page' => 'Notifications',
            'navs' => parent::createNavLinks(SitePage::Notifications, $app),
            'brandText' => parent::brandText($app),
            'brandSrc' => parent::brandImage($app, SitePage::Notifications),
            'notifications' => $notificationsInfo,
            'user' => $user,
            'count' => $count
        ));

        $response->setContent($content);

        return $response;
    }

    public function logoutAction(Application $app) {

        $app['session']->set('id', null);

        return new RedirectResponse('/');
    }

    public function setNotificationSeenAction() {

        $notification = new Notification();
        $notification->setId($_POST['notificationID']);

        DAONotification::getInstance()->updateSeenNotification($notification);

        return new JsonResponse();
    }

    /* PRIVATE METHODS */

    private function sendRegistrationEmail($email, $userID) {

        $href = 'grup9.com/validate/' . $userID;

        $mail = new PHPMailer();

        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPDebug  = 0;
        $mail->SMTPAuth = true;
        $mail->Username = 'pwgramg9@gmail.com';
        $mail->Password = 'bescompany';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('pwgramg9@gmail.com', 'PWGram');
        $mail->addAddress($email);
        $mail->isHTML(true);

        $mail->Subject = 'Confirm your PWGram account';
        $mail->Body    = '<h1>Click the following link to confirm your account</h1><h3><a href=' . $href . '>Start using PWGram!</a></h3>';

        if (!$mail->send()) return $mail->ErrorInfo;
        else return true;
    }

    function compareLikes($a, $b) {
        return strnatcmp($b['numLikes'], $a['numLikes']);
    }

   function compareComments($a, $b) {
       return strnatcmp($b['numComments'], $a['numComments']);
    }

    function compareVisits($a, $b) {
        return strnatcmp($b['visits'], $a['visits']);
    }

    function compareDates($a, $b) {
        return strnatcmp($b['date'], $a['date']);
    }
}