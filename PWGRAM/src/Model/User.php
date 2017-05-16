<?php

namespace SilexApp\Model;

class User {

    /* ************* ATTRIBUTES ****************/
    private $id;
    private $username;
    private $email;
    private $birthdate;
    private $password;
    private $img_path;
    private $active;

    /* ************* CONSTRUCTOR ****************/
    public function __construct() {}

    /* ************* PUBLIC METHODS ****************/
    public function validatePassword(string $password) {

        if (hash('sha512', $password) == $this->password) return true;
        return false;
    }

    public function toArray() {

        return array(
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'birtdate' => $this->birthdate,
            'img_path' => $this->img_path
        );
    }

    /* ************* GETTERS & SETTERS ****************/
    public function getId(): int {

        return $this->id;
    }

    public function setId(int $id) {

        $this->id = $id;
    }

    public function getUsername(): string {

        return $this->username;
    }

    public function setUsername(string $username) {

        $this->username = $username;
    }

    public function getEmail(): string {

        return $this->email;
    }

    public function setEmail(string $email) {

        $this->email = $email;
    }

    public function getBirthdate(): string {

        return $this->birthdate;
    }

    public function setBirthdate(string $birthdate) {

        $this->birthdate = $birthdate;
    }

    public function getPassword(): string {

        return $this->password;
    }

    public function setPassword(string $password) {

        $this->password = $password;
    }

    public function getImgPath(): string {

        return $this->img_path;
    }

    public function setImgPath(string $img_path) {

        $this->img_path = $img_path;
    }

    public function getActive(): string {

        return $this->active;
    }

    public function setActive(int $active) {

        $this->active = $active;
    }
}