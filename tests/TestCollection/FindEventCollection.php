<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/13/16
 * Time: 2:22 PM
 */

namespace CakeMonga\Test\TestCollection;


use Cake\Event\Event;
use Cake\ORM\Entity;
use CakeMonga\MongoCollection\BaseCollection;

class FindEventCollection extends BaseCollection
{
    public $_beforeFind = false;

    public function beforeFind($event, $query, $fields, $findOne)
    {
        $event->result['query'] = ['test' => false];
        $event->result['fields'] = ['test'];
        $this->_beforeFind = true;
    }

}