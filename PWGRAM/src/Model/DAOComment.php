<?php

namespace SilexApp\Model;

use PDO;


class DAOComment {

    /* ************* CONSTANTS ****************/
    private const MYSQL_HOST = '178.62.75.47';
    private const DATABASE_NAME = 'pwgram';
    private const USER_NAME = 'testUser';
    private const USER_PSWD = 'bescompany';

    private const SELECT_STATEMENT = 'SELECT * FROM Commentary WHERE id = :commentID';
    private const SELECT_BY_USER_STATEMENT = 'SELECT * FROM Commentary WHERE user_id = :userID';
    private const SELECT_BY_IMAGE_STATEMENT = 'SELECT * FROM Commentary WHERE image_id = :imageID';
    private const INSERT_STATEMENT = 'INSERT INTO Commentary (text, user_id, image_id) VALUES (:text, :userID, :imageID)';
    private const UPDATE_STATEMENT = 'UPDATE Commentary SET text = :text WHERE id = :commnetID';

    private const COMMENT_ID_REPLACER = ':commentID';
    private const TEXT_REPLACER = ':text';
    private const IMAGE_ID_REPLACER = ':imageID';
    private const USER_ID_REPLACER = ':userID';

    /* ************* ATTRIBUTES ****************/
    private static $instance;
    private $selectStatement;
    private $selectByUserIDStatement;
    private $selectByImageIDStatement;
    private $insertStatement;
    private $updateStatement;

    /* ************* CONSTRUCTOR ****************/
    private function __construct() {

        $dbConnection = new PDO('mysql:host=' . DAOComment::MYSQL_HOST . ';dbname=' . DAOComment::DATABASE_NAME, DAOComment::USER_NAME, DAOComment::USER_PSWD);
        $this->selectStatement = $dbConnection->prepare(DAOComment::SELECT_STATEMENT);
        $this->selectByUserIDStatement = $dbConnection->prepare(DAOComment::SELECT_BY_USER_STATEMENT);
        $this->selectByImageIDStatement = $dbConnection->prepare(DAOComment::SELECT_BY_IMAGE_STATEMENT);
        $this->insertStatement = $dbConnection->prepare(DAOComment::INSERT_STATEMENT);
        $this->updateStatement = $dbConnection->prepare(DAOComment::UPDATE_STATEMENT);
    }

    public static function getInstance(): DAOComment {

        if (DAOComment::$instance == null) DAOComment::$instance = new DAOComment();

        return DAOComment::$instance;
    }

    public function getComment(int $commentID): Comment {

        $this->selectStatement->bindParam(DAOComment::COMMENT_ID_REPLACER, $commentID, PDO::PARAM_INT);
        $this->selectStatement->execute();
        $commentInfo = $this->selectStatement->fetch();

        $comment = new Comment();
        $comment->setId($commentInfo['id']);
        $comment->setText($commentInfo['text']);
        $comment->setUserId($commentInfo['user_id']);
        $comment->setImageId($commentInfo['image_id']);

        return $comment;
    }

    public function getCommentByUserID(int $userID) {

        $this->selectStatement->bindParam(DAOComment::USER_ID_REPLACER, $userID, PDO::PARAM_INT);
        $this->selectStatement->execute();
        $commentInfo = $this->selectStatement->fetchAll();


        $comments = array();

        foreach ($commentInfo as $commentInfoAux) {

            $comment = new Comment();

            $comment->setId($commentInfoAux['id']);
            $comment->setText($commentInfoAux['text']);
            $comment->setUserId($commentInfoAux['user_id']);
            $comment->setImageId($commentInfoAux['image_id']);

            array_push($comments, $comment);
        }


        return $comments;
    }

    public function getCommentByImageID(int $imageID) {

        $this->selectStatement->bindParam(DAOComment::IMAGE_ID_REPLACER, $imageID, PDO::PARAM_INT);
        $this->selectStatement->execute();
        $commentInfo = $this->selectStatement->fetchAll();


        $comments = array();

        foreach ($commentInfo as $commentInfoAux) {

            $comment = new Comment();

            $comment->setId($commentInfoAux['id']);
            $comment->setText($commentInfoAux['text']);
            $comment->setUserId($commentInfoAux['user_id']);
            $comment->setImageId($commentInfoAux['image_id']);

            array_push($comments, $comment);
        }


        return $comments;
    }

    public function insertComment(Comment $comment): void {

        $text = $comment->getText();
        $user_id = $comment->getUserId();
        $image_id = $comment->getImageId();

        $this->insertStatement->bindParam(DAOComment::TEXT_REPLACER, $text, PDO::PARAM_STR);
        $this->insertStatement->bindParam(DAOComment::USER_ID_REPLACER, $user_id, PDO::PARAM_INT);
        $this->insertStatement->bindParam(DAOComment::IMAGE_ID_REPLACER, $image_id, PDO::PARAM_INT);

        $this->insertStatement->execute();
    }

    public function updateComment(Comment $comment): void {

        $text = $comment->getText();
        $id = $comment->getId();

        $this->updateStatement->bindParam(DAOComment::TEXT_REPLACER, $text, PDO::PARAM_STR);
        $this->updateStatement->bindParam(DAOComment::COMMENT_ID_REPLACER, $id, PDO::PARAM_INT);

        $this->updateStatement->execute();
    }
}