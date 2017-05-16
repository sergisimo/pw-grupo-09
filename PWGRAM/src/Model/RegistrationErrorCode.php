<?php
/**
 * Created by PhpStorm.
 * User: borjaperez
 * Date: 15/5/17
 * Time: 11:26
 */

namespace SilexApp\Model;

abstract class RegistrationErrorCode {

    const ErrorCodeUsername = 0;
    const ErrorCodeBirthdate = 1;
    const ErrorCodePassword = 2;
    const ErrorCodeConfirmPassword = 3;
    const ErrorCodeEmail = 4;
    const ErrorCodeUsernameUnavailable = 5;
    const ErrorCodeEmailUnavailable = 6;
    const ErrorCodeRegistrationSuccesful = 7;
}