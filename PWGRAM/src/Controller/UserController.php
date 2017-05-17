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
use SilexApp\Model\LoginErrorCode;
use SilexApp\Model\RegistrationErrorCode;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use SilexApp\Model\SitePage;
use Symfony\Component\HttpFoundation\Request;
use SilexApp\Model\User;
use SilexApp\Model\DAOUser;

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

    public function testEmail() {

        $this->sendRegistrationEmail('ssimo@salleurl.edu', 1);
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

        $content = $app['twig']->render('myProfile.twig', array(
            'page' => 'My profile',
            'navs' => parent::createNavLinks(SitePage::MyProfile, $app),
            'brandText' => parent::brandText($app),
            'brandSrc' => parent::brandImage($app, SitePage::MyProfile),
            'user' => $user,
            'count' => 2
        ));

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');

        if ($app['session']->get('id') != null) $response->setContent($content);
        else $response->setContent(parent::deniedContent($app, 'You must be authenticated in order to view your profile', SitePage::MyProfile));

        return $response;
    }

    public function publicProfileAction(Application $app, Request $request) {

        $profileId = $request->get('id');
        $userInfo = DAOUser::getInstance()->getUserById($profileId);

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
            'username' => $userInfo->getUsername(),
            'birthdate' => $userInfo->getBirthDate(),
            'profileImage' => $userInfo->getImgPath(),
            'userId' => $userInfo->getId(),
            'commentsAmount' => 1,
            'postsAmount' => 3,
            'posts' => $posts
        );

        $content = $app['twig']->render('profile.twig', array(
            'page' => $user['username'],
            'navs' => parent::createNavLinks(SitePage::MyProfile, $app),
            'brandText' => parent::brandText($app),
            'brandSrc' => parent::brandImage($app, SitePage::SecondLevel),
            'user' => $user,
            'count' => 2
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
            'app' => $app,
            'page' => 'My posts',
            'posts' => $posts,
            'navs' => parent::createNavLinks(SitePage::MyPosts, $app),
            'brandText' => parent::brandText($app),
            'brandSrc' => parent::brandImage($app, SitePage::MyPosts)
        ));

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');

        if ($app['session']['active']) $response->setContent($content);
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
            'app' => $app,
            'page' => 'My comments',
            'navs' => parent::createNavLinks(SitePage::MyComments, $app),
            'brandText' => parent::brandText($app),
            'brandSrc' => parent::brandImage($app, SitePage::MyComments),
            'comments' => $comments
        ));

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');

        if ($app['session']['active']) $response->setContent($content);
        else $response->setContent(parent::deniedContent($app, 'You must be authenticated in order to view your comments', SitePage::MyComments));

        return $response;
    }

    public function notificationsAction(Application $app) {

        $notifications = array(
            array(
                'id' => 1,
                'imageSrc' => 'assets/images/test_thubnail.JPG',
                'comment' => 'Pff quin home més sexy!!!',
                'postName' => 'Pussy distroyer',
                'postPage' => '/post/view/1',
                'sourceUserProfile' => 'profile/1',
                'sourceUsername' => 'sanfe'
            ),
            array(
                'id' => 2,
                'imageSrc' => 'assets/images/test2_thubnail.png',
                'postName' => 'Els fotògrafs',
                'postPage' => '/post/view/1',
                'sourceUserProfile' => 'profile/1',
                'sourceUsername' => 'sanfe'
            )
        );

        $content = $app['twig']->render('notifications.twig', array(
            'app' => $app,
            'page' => 'Notifications',
            'navs' => parent::createNavLinks(SitePage::Notifications, $app),
            'brandText' => parent::brandText($app),
            'brandSrc' => parent::brandImage($app, SitePage::Notifications),
            'notifications' => $notifications
        ));

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');

        if ($app['session']['active']) $response->setContent($content);
        else $response->setContent(parent::deniedContent($app, 'You must be authenticated in order to view your notifications', SitePage::Notifications));

        return $response;
    }

    public function logoutAction(Application $app) {

        $app['session']->set('id', null);

        return new RedirectResponse('/');
    }

    /* PRIVATE METHODS */

    private function sendRegistrationEmail($email, $userID) {

        $href = 'grup09.com/validate/' . $userID;

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
}