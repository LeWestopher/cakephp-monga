<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/31/16
 * Time: 3:20 PM
 */

namespace CakeMonga\Test\TestCollection;


use CakeMonga\MongoCollection\MongoBehavior;

class TestBehavior extends MongoBehavior
{
    public function getHelloWorld()
    {
        return 'Hello World!';
    }

    public function beforeSave($event, $document)
    {
        $event->result['document'] = ['test' => true, 'check' => 1];
    }
}