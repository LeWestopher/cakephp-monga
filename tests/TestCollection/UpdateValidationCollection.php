<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/4/16
 * Time: 3:11 AM
 */

namespace CakeMonga\Test\TestCollection;


use Cake\Validation\Validator;
use CakeMonga\MongoCollection\BaseCollection;

class UpdateValidationCollection extends BaseCollection
{
    public function validationDefault(Validator $validator)
    {
        $validator->notEmpty('test', 'Test cannot be empty', 'update');
        return $validator;
    }
}