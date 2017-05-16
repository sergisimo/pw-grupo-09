<?php

namespace SilexApp\Model;

class Comment {

    /* ************* ATTRIBUTES ****************/
    private $id;
    private $text;
    private $user_id;
    private $image_id;

    /* ************* CONSTRUCTOR ****************/
    public function __construct() {}

    /* ************* PUBLIC METHODS ****************/
    public function toArray() {

        return array(
            'id' => $this->id,
            'text' => $this->text,
            'user_id' => $this->user_id,
            'image_id' => $this->image_id
        );
    }

    /* ************* GETTERS & SETTERS ****************/
    public function getId(): int {

        return $this->id;
    }

    public function setId(int $id) {

        $this->id = $id;
    }

    public function getText(): string {

        return $this->text;
    }

    public function setText(string $text) {

        $this->text = $text;
    }

    public function getUserId(): int {

        return $this->user_id;
    }

    public function setUserId(int $user_id) {

        $this->user_id = $user_id;
    }

    public function getImageId(): int {

        return $this->image_id;
    }

    public function setImageId(int $image_id) {

        $this->image_id = $image_id;
    }
}