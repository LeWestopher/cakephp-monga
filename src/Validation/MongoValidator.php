<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/4/16
 * Time: 3:04 AM
 */

namespace CakeMonga\Validation;


use Cake\Validation\Validator;

class MongoValidator extends Validator
{
    public function __construct()
    {
        $this->_useI18n = false;
    }
}