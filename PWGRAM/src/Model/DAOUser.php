<?php

namespace SilexApp\Model;

use PDO;

class DAOUser {

    /* ************* CONSTANTS ****************/
    private const MYSQL_HOST = '178.62.75.47';
    private const DATABASE_NAME = 'pwgram';
    private const USER_NAME = 'testUser';
    private const USER_PSWD = 'bescompany';

    private const SELECT_BY_ID_STATEMENT = 'SELECT * FROM User WHERE id = :userID';
    private const SELECT_STATEMENT = 'SELECT * FROM User WHERE username = :username OR email = :email';
    private const INSERT_STATEMENT = 'INSERT INTO User (username, email, birthdate, password, img_path, active) VALUES (:username, :email, :birthdate, :password, :img_path, :active)';
    private const UPDATE_STATEMENT = 'UPDATE User SET username = :username, email = :email, birthdate = :birthdate, password = :password, img_path = :img_path, active = :active WHERE id = :userID';

    private const USER_ID_REPLACER = ':userID';
    private const USERNAME_REPLACER = ':username';
    private const EMAIL_REPLACER = ':email';
    private const BIRTHDATE_REPLACER = ':birthdate';
    private const PASSWORD_REPLACER = ':password';
    private const IMG_PATH_REPLACER = ':img_path';
    private const ACTIVE_REPLACER = ':active';

    /* ************* ATTRIBUTES ****************/
    private static $instance;
    private $selectStatement;
    private $selectByIdStatement;
    private $insertStatement;
    private $updateStatement;

    /* ************* CONSTRUCTOR ****************/
    private function __construct() {

        $dbConnection = new PDO('mysql:host=' . DAOUser::MYSQL_HOST . ';dbname=' . DAOUser::DATABASE_NAME, DAOUser::USER_NAME, DAOUser::USER_PSWD);
        $this->selectStatement = $dbConnection->prepare(DAOUser::SELECT_STATEMENT);
        $this->selectByIdStatement = $dbConnection->prepare(DAOUser::SELECT_BY_ID_STATEMENT);
        $this->insertStatement = $dbConnection->prepare(DAOUser::INSERT_STATEMENT);
        $this->updateStatement = $dbConnection->prepare(DAOUser::UPDATE_STATEMENT);
    }

    public static function getInstance(): DAOUser {

        if (DAOUser::$instance == null) DAOUser::$instance = new DAOUser();

        return DAOUser::$instance;
    }

    /* ************* PUBLIC METHODS ****************/
    public function getUser(string $username): User {

        $this->selectStatement->bindParam(DAOUser::USERNAME_REPLACER, $username, PDO::PARAM_STR);
        $this->selectStatement->bindParam(DAOUser::EMAIL_REPLACER, $username, PDO::PARAM_STR);
        $this->selectStatement->execute();
        $userInfo = $this->selectStatement->fetch();

        if ($userInfo['id'] != null) {

            $user = new User();
            $user->setId($userInfo['id']);
            $user->setUsername($userInfo['username']);
            $user->setEmail($userInfo['email']);
            $user->setBirthdate($userInfo['birthdate']);
            $user->setPassword($userInfo['password']);
            $user->setImgPath($userInfo['img_path']);
            $user->setActive($userInfo['active']);

            return $user;
        }

        return null;
    }

    public function getUserById(int $userID): User {

        $this->selectByIdStatement->bindParam(DAOUser::USER_ID_REPLACER, $userID, PDO::PARAM_INT);
        $this->selectByIdStatement->execute();
        $userInfo = $this->selectByIdStatement->fetch();

        $user = new User();
        $user->setId($userInfo['id']);
        $user->setUsername($userInfo['username']);
        $user->setEmail($userInfo['email']);
        $user->setBirthdate($userInfo['birthdate']);
        $user->setPassword($userInfo['password']);
        $user->setImgPath($userInfo['img_path']);
        $user->setActive($userInfo['active']);

        return $user;
    }

    public function insertUser(User $user): void {

        $username = $user->getUsername();
        $email = $user->getEmail();
        $birthdate = $user->getBirthdate();
        $password = $user->getPassword();
        $img_path = $user->getImgPath();
        $active = $user->getActive();

        $this->insertStatement->bindParam(DAOUser::USERNAME_REPLACER, $username, PDO::PARAM_STR);
        $this->insertStatement->bindParam(DAOUser::EMAIL_REPLACER, $email, PDO::PARAM_STR);
        $this->insertStatement->bindParam(DAOUser::BIRTHDATE_REPLACER, $birthdate, PDO::PARAM_STR);
        $this->insertStatement->bindParam(DAOUser::PASSWORD_REPLACER, $password, PDO::PARAM_STR);
        $this->insertStatement->bindParam(DAOUser::IMG_PATH_REPLACER, $img_path, PDO::PARAM_STR);
        $this->insertStatement->bindParam(DAOUser::ACTIVE_REPLACER, $active, PDO::PARAM_INT);

        $this->insertStatement->execute();
    }

    public function updateUser(User $user): void {

        $username = $user->getUsername();
        $email = $user->getEmail();
        $birthdate = $user->getBirthdate();
        $password = $user->getPassword();
        $img_path = $user->getImgPath();
        $active = $user->getActive();
        $userID = $user->getId();

        $this->updateStatement->bindParam(DAOUser::USERNAME_REPLACER, $username, PDO::PARAM_STR);
        $this->updateStatement->bindParam(DAOUser::EMAIL_REPLACER, $email, PDO::PARAM_STR);
        $this->updateStatement->bindParam(DAOUser::BIRTHDATE_REPLACER, $birthdate, PDO::PARAM_STR);
        $this->updateStatement->bindParam(DAOUser::PASSWORD_REPLACER, $password, PDO::PARAM_STR);
        $this->updateStatement->bindParam(DAOUser::IMG_PATH_REPLACER, $img_path, PDO::PARAM_STR);
        $this->updateStatement->bindParam(DAOUser::ACTIVE_REPLACER, $active, PDO::PARAM_INT);
        $this->updateStatement->bindParam(DAOUser::USER_ID_REPLACER, $userID, PDO::PARAM_INT);

        $this->updateStatement->execute();
    }
}