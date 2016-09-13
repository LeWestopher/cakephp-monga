<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/23/16
 * Time: 10:02 PM
 */

namespace CakeMonga\MongoEntity;


trait MongoEntityConverter
{
    public function toMongo()
    {
        $rc_array = new RecursiveArrayObject($this);
        return $rc_array->getArrayCopy();
    }
}