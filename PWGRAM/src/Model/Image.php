<?php

namespace SilexApp\Model;

class Image {

    /* ************* ATTRIBUTES ****************/
    private $id;
    private $user_id;
    private $title;
    private $img_path;
    private $visits;
    private $private;
    private $created_at;

    /* ************* CONSTRUCTOR ****************/
    public function __construct() {}

    /* ************* PUBLIC METHODS ****************/
    public function toArray() {

        return array(
            'id' => $this->id,
            'title' => $this->title,
            'img_path' => $this->img_path,
            'visits' => $this->visits,
            'private' => $this->private,
            'created_at' => $this->created_at
        );
    }

    /* ************* GETTERS & SETTERS ****************/
    public function getId(): int {

        return $this->id;
    }

    public function setId(int $id) {

        $this->id = $id;
    }

    public function getUserId(): int {

        return $this->user_id;
    }

    public function setUserId(int $user_id) {

        $this->user_id = $user_id;
    }

    public function getTitle(): string {

        return $this->title;
    }

    public function setTitle(string $title) {

        $this->title = $title;
    }

    public function getImgPath(): string {

        return $this->img_path;
    }

    public function setImgPath(string $img_path) {

        $this->img_path = $img_path;
    }

    public function getVisits(): int {

        return $this->visits;
    }

    public function setVisits(int $visits) {

        $this->visits = $visits;
    }

    public function getPrivate(): int {

        return $this->private;
    }

    public function setPrivate(int $private) {

        $this->private = $private;
    }

    public function getCreatedAt(): string {

        return $this->created_at;
    }

    public function setCreatedAt(string $created_at) {

        $this->created_at = $created_at;
    }
}