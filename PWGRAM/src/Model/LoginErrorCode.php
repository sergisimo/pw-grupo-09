<?php
/**
 * Created by PhpStorm.
 * User: borjaperez
 * Date: 13/5/17
 * Time: 11:26
 */

namespace SilexApp\Model;

abstract class LoginErrorCode {

    const ErrorCodeUsername = 0;
    const ErrorCodePassword = 1;
    const ErrorCodeNotFound = 2;
    const ErrorCodeNotConfirmed = 3;
}