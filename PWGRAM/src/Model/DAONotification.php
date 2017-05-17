<?php
/**
 * Created by PhpStorm.
 * User: sergi
 * Date: 16/5/17
 * Time: 21:55
 */

namespace SilexApp\Model;

use PDO;

class DAONotification {
    /* ************* CONSTANTS ****************/
    private const MYSQL_HOST = '178.62.75.47';
    private const DATABASE_NAME = 'pwgram';
    private const USER_NAME = 'testUser';
    private const USER_PSWD = 'bescompany';

    private const SELECT_STATEMENT = 'SELECT * FROM Notification WHERE user_id = :userID && seen = 0';
    private const INSERT_STATEMENT = 'INSERT INTO Notification (text, user_id, image_id, seen) VALUES (:text, :userID, :imageID, 0)';
    private const UPDATE_STATEMENT = 'UPDATE Notification SET text = :text WHERE user_id = :userID && image_id = :imageID';
    private const UPDATE_SEEN_STATEMENT = 'UPDATE Notification SET seen = 1 WHERE id = :notificationID';
    private const DELETE_STATEMENT = 'DELETE FROM Notification WHERE user_id = :userID && image_id = :imageID';

    private const NOTIFICATION_ID_REPLACER = ':notificationID';
    private const TEXT_REPLACER = ':text';
    private const IMAGE_ID_REPLACER = ':imageID';
    private const USER_ID_REPLACER = ':userID';

    /* ************* ATTRIBUTES ****************/
    private static $instance;
    private $selectStatement;
    private $insertStatement;
    private $updateStatement;
    private $updateSeenStatement;
    private $deleteStatement;

    /* ************* CONSTRUCTOR ****************/
    private function __construct() {

        $dbConnection = new PDO('mysql:host=' . DAONotification::MYSQL_HOST . ';dbname=' . DAONotification::DATABASE_NAME, DAONotification::USER_NAME, DAONotification::USER_PSWD);
        $this->selectStatement = $dbConnection->prepare(DAONotification::SELECT_STATEMENT);
        $this->insertStatement = $dbConnection->prepare(DAONotification::INSERT_STATEMENT);
        $this->updateStatement = $dbConnection->prepare(DAONotification::UPDATE_STATEMENT);
        $this->updateSeenStatement = $dbConnection->prepare(DAONotification::UPDATE_SEEN_STATEMENT);
        $this->deleteStatement = $dbConnection->prepare(DAONotification::DELETE_STATEMENT);
    }

    public static function getInstance(): DAONotification {

        if (DAONotification::$instance == null) DAONotification::$instance = new DAONotification();

        return DAONotification::$instance;
    }

    public function getNotificationsByUser(int $userID) {

        $this->selectStatement->bindParam(DAONotification::USER_ID_REPLACER, $userID, PDO::PARAM_INT);
        $this->selectStatement->execute();
        $notificationInfo = $this->selectStatement->fetchAll();


        $notifications = array();

        foreach ($notificationInfo as $notificationInfoAux) {

            $notification = new Notification();

            $notification->setId($notificationInfoAux['id']);
            $notification->setText($notificationInfoAux['text']);
            $notification->setUserId($notificationInfoAux['user_id']);
            $notification->setImageId($notificationInfoAux['image_id']);

            array_push($notifications, $notification);
        }

        return $notifications;
    }

    public function insertNotification(Notification $notification): void {

        $text = $notification->getText();
        $user_id = $notification->getUserId();
        $image_id = $notification->getImageId();

        $this->insertStatement->bindParam(DAONotification::TEXT_REPLACER, $text, PDO::PARAM_STR);
        $this->insertStatement->bindParam(DAONotification::USER_ID_REPLACER, $user_id, PDO::PARAM_INT);
        $this->insertStatement->bindParam(DAONotification::IMAGE_ID_REPLACER, $image_id, PDO::PARAM_INT);

        $this->insertStatement->execute();
    }

    public function updateNotification(Notification $notification): void {

        $text = $notification->getText();
        $user_id = $notification->getUserId();
        $image_id = $notification->getImageId();

        $this->updateStatement->bindParam(DAONotification::TEXT_REPLACER, $text, PDO::PARAM_STR);
        $this->updateStatement->bindParam(DAONotification::USER_ID_REPLACER, $user_id, PDO::PARAM_INT);
        $this->updateStatement->bindParam(DAONotification::IMAGE_ID_REPLACER, $image_id, PDO::PARAM_INT);

        $this->updateStatement->execute();
    }

    public function updateSeenNotification(Notification $notification): void {

        $id = $notification->getId();

        $this->updateSeenStatement->bindParam(DAONotification::NOTIFICATION_ID_REPLACER, $id, PDO::PARAM_INT);

        $this->updateStatement->execute();
    }

    public function deleteNotification(Notification $notification): void {

        $user_id = $notification->getUserId();
        $image_id = $notification->getImageId();

        $this->updateSeenStatement->bindParam(DAONotification::USER_ID_REPLACER, $user_id, PDO::PARAM_INT);
        $this->updateSeenStatement->bindParam(DAONotification::IMAGE_ID_REPLACER, $image_id, PDO::PARAM_INT);

        $this->updateStatement->execute();
    }
}