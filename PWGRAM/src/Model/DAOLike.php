<?php

namespace SilexApp\Model;

use PDO;

class DAOLike {

    /* ************* CONSTANTS ****************/
    private const MYSQL_HOST = '178.62.75.47';
    private const DATABASE_NAME = 'pwgram';
    private const USER_NAME = 'testUser';
    private const USER_PSWD = 'bescompany';

    private const SELECT_BY_USER_STATEMENT = 'SELECT * FROM Like WHERE user_id = :userID';
    private const SELECT_BY_IMAGE_STATEMENT = 'SELECT * FROM Like WHERE image_id = :imageID';
    private const INSERT_STATEMENT = 'INSERT INTO Like (user_id, image_id) VALUES (:userID, :imageID)';
    private const DELETE_STATEMENT = 'DELETE FROM Like WHERE id = :commnetID';

    private const LIKE_ID_REPLACER = ':likeID';
    private const IMAGE_ID_REPLACER = ':imageID';
    private const USER_ID_REPLACER = ':userID';

    /* ************* ATTRIBUTES ****************/
    private static $instance;
    private $selectByUserIDStatement;
    private $selectByImageIDStatement;
    private $insertStatement;
    private $deleteStatement;

    /* ************* CONSTRUCTOR ****************/
    private function __construct() {

        $dbConnection = new PDO('mysql:host=' . DAOLike::MYSQL_HOST . ';dbname=' . DAOLike::DATABASE_NAME, DAOLike::USER_NAME, DAOLike::USER_PSWD);
        $this->selectByUserIDStatement = $dbConnection->prepare(DAOLike::SELECT_BY_USER_STATEMENT);
        $this->selectByImageIDStatement = $dbConnection->prepare(DAOLike::SELECT_BY_IMAGE_STATEMENT);
        $this->insertStatement = $dbConnection->prepare(DAOLike::INSERT_STATEMENT);
        $this->deleteStatement = $dbConnection->prepare(DAOLike::DELETE_STATEMENT);
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

        $this->selectByUserIDStatement->bindParam(DAOLike::IMAGE_ID_REPLACER, $imageID, PDO::PARAM_INT);
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

    public function insertLike(Like $like): void {

        $user_id = $like->getUserId();
        $image_id = $like->getImageId();

        $this->insertStatement->bindParam(DAOLike::USER_ID_REPLACER, $user_id, PDO::PARAM_INT);
        $this->insertStatement->bindParam(DAOLike::IMAGE_ID_REPLACER, $image_id, PDO::PARAM_INT);

        $this->insertStatement->execute();
    }

    public function deleteLike(Like $like): void {

        $id = $like->getId();

        $this->deleteStatement->bindParam(DAOLike::LIKE_ID_REPLACER, $id, PDO::PARAM_INT);

        $this->deleteStatement->execute();
    }
}