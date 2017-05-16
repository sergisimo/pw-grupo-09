<?php
/**
 * Created by PhpStorm.
 * User: sergi
 * Date: 15/5/17
 * Time: 22:04
 */

namespace SilexApp\Model;

use PDO;

class DAOImage {


    /* ************* CONSTANTS ****************/
    private const MYSQL_HOST = '178.62.75.47';
    private const DATABASE_NAME = 'pwgram';
    private const USER_NAME = 'testUser';
    private const USER_PSWD = 'bescompany';

    private const SELECT_STATEMENT = 'SELECT * FROM Image WHERE id = :imageID';
    private const SELECT_ALL_STATEMENT = 'SELECT * FROM Image';
    private const SELECT_BY_USER_STATEMENT = 'SELECT * FROM Image WHERE user_id = :userID';
    private const INSERT_STATEMENT = 'INSERT INTO Image (user_id, title, img_path, visits, private, created_at) VALUES (:userID, :title, :img_path, 0, :private, NOW())';
    private const UPDATE_STATEMENT = 'UPDATE Image SET title = :title, img_path = :img_path, private = :private WHERE id = :imageID';
    private const UPDATE_VISIT_STATEMENT = 'UPDATE Image SET visits = :visits WHERE id = :imageID';

    private const IMAGE_ID_REPLACER = ':imageID';
    private const USER_ID_REPLACER = ':userID';
    private const TITLE_REPLACER = ':title';
    private const IMG_PATH_REPLACER = ':img_path';
    private const VISITS_REPLACER = ':visits';
    private const PRIVATE_REPLACER = ':private';

    /* ************* ATTRIBUTES ****************/
    private static $instance;
    private $selectStatement;
    private $selectAllStatement;
    private $selectByUserIDStatement;
    private $insertStatement;
    private $updateStatement;
    private $updateVisitsStatement;

    /* ************* CONSTRUCTOR ****************/
    private function __construct() {

        $dbConnection = new PDO('mysql:host=' . DAOImage::MYSQL_HOST . ';dbname=' . DAOImage::DATABASE_NAME, DAOImage::USER_NAME, DAOImage::USER_PSWD);
        $this->selectStatement = $dbConnection->prepare(DAOImage::SELECT_STATEMENT);
        $this->selectAllStatement = $dbConnection->prepare(DAOImage::SELECT_ALL_STATEMENT);
        $this->selectByUserIDStatement = $dbConnection->prepare(DAOImage::SELECT_BY_USER_STATEMENT);
        $this->insertStatement = $dbConnection->prepare(DAOImage::INSERT_STATEMENT);
        $this->updateStatement = $dbConnection->prepare(DAOImage::UPDATE_STATEMENT);
        $this->updateVisitsStatement = $dbConnection->prepare(DAOImage::UPDATE_VISIT_STATEMENT);
    }

    public static function getInstance(): DAOImage {

        if (DAOImage::$instance == null) DAOImage::$instance = new DAOImage();

        return DAOImage::$instance;
    }

    /* ************* PUBLIC METHODS ****************/
    public function getImage(int $imageID): Image {

        $this->selectStatement->bindParam(DAOImage::IMAGE_ID_REPLACER, $imageID, PDO::PARAM_INT);
        $this->selectStatement->execute();
        $imageInfo = $this->selectStatement->fetch();

        $image = new Image();
        $image->setId($imageInfo['id']);
        $image->setUserId($imageInfo['user_id']);
        $image->setTitle($imageInfo['title']);
        $image->setImgPath($imageInfo['img_path']);
        $image->setVisits($imageInfo['visits']);
        $image->setPrivate($imageInfo['private']);
        $image->setCreatedAt($imageInfo['created_at']);

        return $image;
    }

    public function getImages() {

        $this->selectAllStatement->execute();
        $imageInfo = $this->selectAllStatement->fetchAll();

        $images = array();

        foreach ($imageInfo as $image) {

            $imageAux = new Image();
            $imageAux->setId($image['id']);
            $imageAux->setUserId($image['user_id']);
            $imageAux->setTitle($image['title']);
            $imageAux->setImgPath($image['img_path']);
            $imageAux->setVisits($image['visits']);
            $imageAux->setPrivate($image['private']);
            $imageAux->setCreatedAt($image['created_at']);

            array_push($images, $imageAux);
        }

        return $images;
    }

    public function getImagesByUserID(int $userID) {

        $this->selectByUserIDStatement->bindParam(DAOImage::USER_ID_REPLACER, $userID, PDO::PARAM_INT);
        $this->selectByUserIDStatement->execute();
        $imageInfo = $this->selectByUserIDStatement->fetchAll();

        $images = array();

        foreach ($imageInfo as $image) {

            $imageAux = new Image();
            $imageAux->setId($image['id']);
            $imageAux->setUserId($image['user_id']);
            $imageAux->setTitle($image['title']);
            $imageAux->setImgPath($image['img_path']);
            $imageAux->setVisits($image['visits']);
            $imageAux->setPrivate($image['private']);
            $imageAux->setCreatedAt($image['created_at']);

            array_push($images, $imageAux);
        }

        return $images;
    }

    public function insertImage(Image $image): void {

        $user_id = $image->getUserId();
        $title = $image->getTitle();
        $img_path = $image->getImgPath();
        $private = $image->getPrivate();

        $this->insertStatement->bindParam(DAOImage::USER_ID_REPLACER, $user_id, PDO::PARAM_INT);
        $this->insertStatement->bindParam(DAOImage::TITLE_REPLACER, $title, PDO::PARAM_STR);
        $this->insertStatement->bindParam(DAOImage::IMG_PATH_REPLACER, $img_path, PDO::PARAM_STR);
        $this->insertStatement->bindParam(DAOImage::PRIVATE_REPLACER, $private, PDO::PARAM_INT);

        $this->insertStatement->execute();
    }

    public function updateImage(Image $image): void {

        $title = $image->getTitle();
        $img_path = $image->getImgPath();
        $private = $image->getPrivate();
        $id = $image->getId();

        $this->updateStatement->bindParam(DAOImage::TITLE_REPLACER, $title, PDO::PARAM_STR);
        $this->updateStatement->bindParam(DAOImage::IMG_PATH_REPLACER, $img_path, PDO::PARAM_STR);
        $this->updateStatement->bindParam(DAOImage::PRIVATE_REPLACER, $private, PDO::PARAM_INT);
        $this->updateStatement->bindParam(DAOImage::IMAGE_ID_REPLACER, $id, PDO::PARAM_INT);

        $this->updateStatement->execute();
    }

    public function updateImageVisits(Image $image): void {

        $visits = $image->getVisits();
        $id = $image->getId();

        $this->updateVisitsStatement->bindParam(DAOImage::VISITS_REPLACER, $visits, PDO::PARAM_INT);
        $this->updateVisitsStatement->bindParam(DAOImage::IMAGE_ID_REPLACER, $id, PDO::PARAM_INT);

        $this->updateVisitsStatement->execute();
    }
}