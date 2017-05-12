<?php
/**
 * Created by PhpStorm.
 * User: Esteve Genovart
 * Date: 29/03/2017
 * Time: 19:10
 */

$app->get('/','SilexApp\\Controller\\HomeController::indexAction');
$app->get('/myProfile','SilexApp\\Controller\\UserController::myProfileAction');
$app->get('/addPost','SilexApp\\Controller\\UserController::addPostAction');
$app->get('/myPosts','SilexApp\\Controller\\UserController::myPostsAction');
$app->get('/myComents','SilexApp\\Controller\\UserController::myCommentsAction');
$app->get('/notifications','SilexApp\\Controller\\UserController::notificationsAction');
$app->get('/post/edit/{id}','SilexApp\\Controller\\ImageController::editPostAction');
$app->get('/post/view/{id}','SilexApp\\Controller\\ImageController::viewPostAction');