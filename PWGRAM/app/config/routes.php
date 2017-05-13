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