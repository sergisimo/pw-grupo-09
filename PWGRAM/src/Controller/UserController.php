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
        $user->setImgPath($_POST['profileImage']);

        $errors = $user->validateRegistration();

        if (count($errors) == 0) {
            $user->setPassword(hash('sha512', $_POST['password']));
            DAOUser::getInstance()->insertUser($user);
            array_push($errors, RegistrationErrorCode::ErrorCodeRegistrationSuccesful);

            $this->sendRegistrationEmail($user->getEmail(), $user->getUsername(), $user->getId());
        }

        $response = new JsonResponse($errors);

        return $response;
    }

    public function validateAccountAction(Application $app, Request $request) {

        $id = $request->get('id');

        $user = DAOUser::getInstance()->getUserById($id);
        $user->setActive(1);

        DAOUser::getInstance()->updateUser($user);

        $app['session']['active'] = true;
        $app['session']['user'] = $user;

        $response = new RedirectResponse('/');
        return $response;
    }

    public function loginAction() {

        $errors = array();

        $user = DAOUser::getInstance()->getUser($_POST['username']);

        if ($user == null) array_push($errors, LoginErrorCode::ErrorCodeNotFound);
        else if (!$user->validatePassword($_POST['password'])) array_push($errors, LoginErrorCode::ErrorCodeNotFound);
        else if (!$user->getActive()) array_push($errors, LoginErrorCode::ErrorCodeNotConfirmed);

        $response = new JsonResponse($errors);

        return $response;
    }

    public function myProfileAction(Application $app) {

        $user = array(
            'username' => 'bperezme',
            'birthdate' => '1995-10-03',
            'profileImage' => 'assets/images/defaultProfile.png',
            'selfUser' => 'true'
        );

        $content = $app['twig']->render('profile.twig', array(
            'app' => $app,
            'page' => 'My profile',
            'navs' => parent::createNavLinks(SitePage::MyProfile, $app),
            'brandText' => parent::brandText($app),
            'brandSrc' => parent::brandImage($app, SitePage::MyProfile),
            'user' => $user
        ));

        $response = new Response();
        $response->setStatusCode($response::HTTP_OK);
        $response->headers->set('Content-Type','text/html');

        if ($app['session']['active']) $response->setContent($content);
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
            'app' => $app,
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


    /* PRIVATE METHODS */

    private function sendRegistrationEmail($email, $username, $userID) {

        $href = '/validate/' . $userID;

        $mail = new PHPMailer();


        var_dump('1');

        $mail->isSMTP();
        $mail->Host = 'smtp-relay.gmail.com;smtp.gmail.com;aspmx.l.google.com';
        $mail->SMTPDebug  = 2;
        $mail->SMTPAuth = true;
        $mail->Username = 'pwgramg9@gmail.com';
        $mail->Password = 'bescompany';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        var_dump('2');

        $mail->setFrom('pwgramg9@gmail.com', 'PWGram');
        $mail->addAddress($email, $username);
        $mail->isHTML(false);

        var_dump('3');

        $mail->Subject = 'Confirm your PWGram account';
        //$mail->Body    = 'Click the following link to confirm your account <h7 class="font-weight-bold text-primary"><a href=' . $href . '>Start using PWGram!</a></h7></p>';
        $mail->Body = 'Prova';


        if (!$mail->send()) {
            //echo 'Message could not be sent.';
            //echo 'Mailer Error: ' . $mail->ErrorInfo;
        }
        else {
            //echo 'Message has been sent';
        }
    }
}