<?php
/**
 * Created by PhpStorm.
 * User: Esteve Genovart
 * Date: 29/03/2017
 * Time: 19:10
 */

$app->get('/','SilexApp\\Controller\\HomeController::indexAction');
$app->get('/myProfile','SilexApp\\Controller\\UserController::myProfileAction');
$app->get('/profile/{id}','SilexApp\\Controller\\UserController::publicProfileAction');
$app->get('/myPosts','SilexApp\\Controller\\UserController::myPostsAction');
$app->get('/myComments','SilexApp\\Controller\\UserController::myCommentsAction');
$app->get('/notifications','SilexApp\\Controller\\UserController::notificationsAction');
$app->get('/post/edit/{id}','SilexApp\\Controller\\PostController::editPostAction');
$app->get('/post/view/{id}','SilexApp\\Controller\\PostController::viewPostAction');
$app->get('/addPost','SilexApp\\Controller\\PostController::addPostAction');
$app->post('/register','SilexApp\\Controller\\UserController::registerAction');
$app->post('/login','SilexApp\\Controller\\UserController::loginAction');
$app->get('/validate/{id}','SilexApp\\Controller\\UserController::validateAccountAction');
$app->get('/logout','SilexApp\\Controller\\UserController::logoutAction');
$app->post('/uploadImage','SilexApp\\Controller\\UserController::uploadImageAction');
$app->post('/uploadPostImage','SilexApp\\Controller\\PostController::uploadImageAction');
$app->post('/uploadPost','SilexApp\\Controller\\PostController::uploadPostAction');
$app->post('/updateUser','SilexApp\\Controller\\UserController::updateUserAction');
$app->post('/editPost','SilexApp\\Controller\\PostController::updatePostInfoAction');
$app->post('/deletePost','SilexApp\\Controller\\PostController::deletePostAction');
$app->post('/likePost','SilexApp\\Controller\\PostController::likePostAction');
$app->post('/unlikePost','SilexApp\\Controller\\PostController::unlikePostAction');
$app->post('/commentPost','SilexApp\\Controller\\PostController::commentPostAction');
$app->post('/uncommentPost','SilexApp\\Controller\\PostController::uncommentPostAction');
$app->post('/editComment','SilexApp\\Controller\\PostController::editCommentPostAction');
$app->post('/notificationSeen','SilexApp\\Controller\\UserController::setNotificationSeenAction');
$app->get('/test','SilexApp\\Controller\\TestController::testing');

