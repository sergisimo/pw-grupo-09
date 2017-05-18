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
    private const SELECT_BY_IMAGE_STATEMENT = 'SELECT * FROM Commentary WHERE image_id = :imageID ORDER BY id DESC';
    private const INSERT_STATEMENT = 'INSERT INTO Commentary (text, user_id, image_id) VALUES (:text, :userID, :imageID)';
    private const UPDATE_STATEMENT = 'UPDATE Commentary SET text = :text WHERE id = :commnetID';
    private const DELETE_STATEMENT = 'DELETE FROM Commentary WHERE id = :commentID';
    private const CHECK_CAN_COMMENT_STATEMENT = 'SELECT * FROM Commentary WHERE user_id = :userID AND image_id = :imageID';

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
    private $deleteStatement;
    private $checkCanCommnetStatement;

    /* ************* CONSTRUCTOR ****************/
    private function __construct() {

        $dbConnection = new PDO('mysql:host=' . DAOComment::MYSQL_HOST . ';dbname=' . DAOComment::DATABASE_NAME, DAOComment::USER_NAME, DAOComment::USER_PSWD);
        $this->selectStatement = $dbConnection->prepare(DAOComment::SELECT_STATEMENT);
        $this->selectByUserIDStatement = $dbConnection->prepare(DAOComment::SELECT_BY_USER_STATEMENT);
        $this->selectByImageIDStatement = $dbConnection->prepare(DAOComment::SELECT_BY_IMAGE_STATEMENT);
        $this->insertStatement = $dbConnection->prepare(DAOComment::INSERT_STATEMENT);
        $this->updateStatement = $dbConnection->prepare(DAOComment::UPDATE_STATEMENT);
        $this->deleteStatement = $dbConnection->prepare(DAOComment::DELETE_STATEMENT);
        $this->checkCanCommnetStatement = $dbConnection->prepare(DAOComment::CHECK_CAN_COMMENT_STATEMENT);
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

        $this->selectByUserIDStatement->bindParam(DAOComment::USER_ID_REPLACER, $userID, PDO::PARAM_INT);
        $this->selectByUserIDStatement->execute();
        $commentInfo = $this->selectByUserIDStatement->fetchAll();


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

        $this->selectByImageIDStatement->bindParam(DAOComment::IMAGE_ID_REPLACER, $imageID, PDO::PARAM_INT);
        $this->selectByImageIDStatement->execute();
        $commentInfo = $this->selectByImageIDStatement->fetchAll();


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

        $notification = new Notification();
        $notification->setText($text);
        $notification->setUserId($user_id);
        $notification->setImageId($image_id);
        DAONotification::getInstance()->insertNotification($notification);
    }

    public function updateComment(Comment $comment): void {

        $text = $comment->getText();
        $id = $comment->getId();

        $this->updateStatement->bindParam(DAOComment::TEXT_REPLACER, $text, PDO::PARAM_STR);
        $this->updateStatement->bindParam(DAOComment::COMMENT_ID_REPLACER, $id, PDO::PARAM_INT);

        $this->updateStatement->execute();

        $notification = new Notification();
        $notification->setUserId($comment->getUserId());
        $notification->setImageId($comment->getImageId());
        $notification->setText($text);

        DAONotification::getInstance()->updateNotification($notification);
    }

    public function deleteComment(Comment $comment): void {

        $ID = $comment->getId();

        $comment = DAOComment::getInstance()->getComment($ID);

        $this->deleteStatement->bindParam(DAOComment::COMMENT_ID_REPLACER, $ID, PDO::PARAM_INT);

        $this->deleteStatement->execute();

        $notification = new Notification();
        $notification->setUserId($comment->getUserId());
        $notification->setImageId($comment->getImageId());

        DAONotification::getInstance()->deleteNotification($notification, true);
    }

    public function checkCanCommnet(int $userID, int $imageID) {

        $this->checkCanCommnetStatement->bindParam(DAOComment::USER_ID_REPLACER, $userID, PDO::PARAM_INT);
        $this->checkCanCommnetStatement->bindParam(DAOComment::IMAGE_ID_REPLACER, $imageID, PDO::PARAM_INT);
        $this->checkCanCommnetStatement->execute();
        $commentInfo = $this->checkCanCommnetStatement->fetch();

        if ($commentInfo['id'] == null) return true;
        return false;
    }
}