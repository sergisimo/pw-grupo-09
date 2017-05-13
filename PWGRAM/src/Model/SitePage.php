<?php

/**
 * Created by PhpStorm.
 * User: borjaperez
 * Date: 10/5/17
 * Time: 13:02
 */

namespace SilexApp\Model;

abstract class SitePage {

    const Home = 0;
    const Register = 1;
    const Login = 2;
    const NotFound = 3;
    const AddPost = 4;
    const MyPosts = 5;
    const MyComments = 6;
    const Notifications = 7;
    const MyProfile = 8;
    const SecondLevel = 9;
    const ThirdLevel = 10;
}