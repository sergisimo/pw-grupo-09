<?php

namespace SilexApp\Controller;

use SilexApp\Model\DAOImage;
use SilexApp\Model\DAOUser;
use SilexApp\Model\Image;
use SilexApp\Model\User;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Symfony\Component\HttpFoundation\Response;

class TestController  extends BaseController {

    public function testing(Application $app, Request $request) {

        $image = new Image();

        $image->setUserId(1);
        $image->setTitle('Pussy Destroyer');
        $image->setImgPath('/images/pussyPOWER');
        $image->setPrivate(1);

        DAOImage::getInstance()->insertImage($image);

        var_dump(DAOImage::getInstance()->getImages());

        return new Response();
    }
}