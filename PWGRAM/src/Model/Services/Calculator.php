<?php
/**
 * Created by PhpStorm.
 * User: Esteve Genovart
 * Date: 19/04/2017
 * Time: 19:18
 */

namespace SilexApp\Model\Services;


class Calculator {

    public function add(int $firstNumber, int $secondNumber){
        return $firstNumber + $secondNumber;
    }
}