<?php

namespace SilexApp\Model;

use PDO;
use function Sodium\library_version_major;

class DAOLike {

    /* ************* CONSTANTS ****************/
    private const MYSQL_HOST = '178.62.75.47';
    private const DATABASE_NAME = 'pwgram';
    private const USER_NAME = 'testUser';
    private const USER_PSWD = 'bescompany';

    private const SELECT_BY_USER_STATEMENT = 'SELECT * FROM LikeTable WHERE user_id = :userID';
    private const SELECT_BY_IMAGE_STATEMENT = 'SELECT * FROM LikeTable WHERE image_id = :imageID';
    private const INSERT_STATEMENT = 'INSERT INTO LikeTable (user_id, image_id) VALUES (:userID, :imageID)';
    private const DELETE_STATEMENT = 'DELETE FROM LikeTable WHERE  user_id = :userID AND image_id = :imageID';
    private const CHECK_LIKED_STATEMENT = 'SELECT * FROM LikeTable WHERE user_id = :userID AND image_id = :imageID';


    private const LIKE_ID_REPLACER = ':likeID';
    private const IMAGE_ID_REPLACER = ':imageID';
    private const USER_ID_REPLACER = ':userID';

    /* ************* ATTRIBUTES ****************/
    private static $instance;
    private $selectByUserIDStatement;
    private $selectByImageIDStatement;
    private $insertStatement;
    private $deleteStatement;
    private $checkLikedStatement;

    /* ************* CONSTRUCTOR ****************/
    private function __construct() {

        $dbConnection = new PDO('mysql:host=' . DAOLike::MYSQL_HOST . ';dbname=' . DAOLike::DATABASE_NAME, DAOLike::USER_NAME, DAOLike::USER_PSWD);
        $this->selectByUserIDStatement = $dbConnection->prepare(DAOLike::SELECT_BY_USER_STATEMENT);
        $this->selectByImageIDStatement = $dbConnection->prepare(DAOLike::SELECT_BY_IMAGE_STATEMENT);
        $this->insertStatement = $dbConnection->prepare(DAOLike::INSERT_STATEMENT);
        $this->deleteStatement = $dbConnection->prepare(DAOLike::DELETE_STATEMENT);
        $this->checkLikedStatement = $dbConnection->prepare(DAOLike::CHECK_LIKED_STATEMENT);
    }

    public static function getInstance(): DAOLike {

        if (DAOLike::$instance == null) DAOLike::$instance = new DAOLike();

        return DAOLike::$instance;
    }

    /* ************* PUBLIC METHODS ****************/
    public function getLikeByUserID(int $userID) {

        $this->selectByUserIDStatement->bindParam(DAOLike::USER_ID_REPLACER, $userID, PDO::PARAM_INT);
        $this->selectByUserIDStatement->execute();
        $likeInfo = $this->selectByUserIDStatement->fetchAll();

        $likes = array();

        foreach ($likeInfo as $likeInfoAux) {

            $like = new Like();

            $like->setId($likeInfoAux['id']);
            $like->setUserId($likeInfoAux['user_id']);
            $like->setImageId($likeInfoAux['image_id']);

            array_push($likes, $like);
        }

        return $likes;
    }

    public function getLikeByImageID(int $imageID) {

        $this->selectByImageIDStatement->bindParam(DAOLike::IMAGE_ID_REPLACER, $imageID, PDO::PARAM_INT);
        $this->selectByImageIDStatement->execute();
        $likeInfo = $this->selectByImageIDStatement->fetchAll();

        $likes = array();

        foreach ($likeInfo as $likeInfoAux) {

            $like = new Like();

            $like->setId($likeInfoAux['id']);
            $like->setUserId($likeInfoAux['user_id']);
            $like->setImageId($likeInfoAux['image_id']);

            array_push($likes, $like);
        }

        return $likes;
    }

    public function insertLike(Like $like): void {

        $user_id = $like->getUserId();
        $image_id = $like->getImageId();

        $this->insertStatement->bindParam(DAOLike::USER_ID_REPLACER, $user_id, PDO::PARAM_INT);
        $this->insertStatement->bindParam(DAOLike::IMAGE_ID_REPLACER, $image_id, PDO::PARAM_INT);

        $this->insertStatement->execute();

        $notification = new Notification();
        $notification->setText("isLike");
        $notification->setUserId($user_id);
        $notification->setImageId($image_id);
        DAONotification::getInstance()->insertNotification($notification);
    }

    public function deleteLike(Like $like): void {

        $image_id = $like->getImageId();
        $user_id = $like->getUserId();

        $this->deleteStatement->bindParam(DAOLike::IMAGE_ID_REPLACER, $image_id, PDO::PARAM_INT);
        $this->deleteStatement->bindParam(DAOLike::USER_ID_REPLACER, $user_id, PDO::PARAM_INT);

        $this->deleteStatement->execute();

        $notification = new Notification();
        $notification->setUserId($like->getUserId());
        $notification->setImageId($like->getImageId());

        DAONotification::getInstance()->deleteNotification($notification, false);
    }

    public function checkIsLiked(int $userID, int $imageID) {

        $this->checkLikedStatement->bindParam(DAOLike::USER_ID_REPLACER, $userID, PDO::PARAM_INT);
        $this->checkLikedStatement->bindParam(DAOLike::IMAGE_ID_REPLACER, $imageID, PDO::PARAM_INT);
        $this->checkLikedStatement->execute();
        $likeInfo = $this->checkLikedStatement->fetch();

        if ($likeInfo['id'] == null) return false;
        return true;
    }
}