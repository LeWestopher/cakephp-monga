<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/13/16
 * Time: 2:22 PM
 */

namespace CakeMonga\Test\TestCollection;


use CakeMonga\MongoCollection\BaseCollection;

class TestsCollection extends BaseCollection
{
    public function world()
    {
        return 'hello';
    }
}