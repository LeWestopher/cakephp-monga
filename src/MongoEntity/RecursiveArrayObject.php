<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/23/16
 * Time: 10:04 PM
 */

namespace CakeMonga\MongoEntity;


class RecursiveArrayObject extends \ArrayObject
{
    function getArrayCopy()
    {
        $resultArray = parent::getArrayCopy();
        foreach($resultArray as $key => $val) {
            if (!is_object($val)) {
                continue;
            }
            $o = new RecursiveArrayObject($val);
            $resultArray[$key] = $o->getArrayCopy();
        }
        return $resultArray;
    }
}