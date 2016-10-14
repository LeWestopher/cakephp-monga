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

class DeleteEventCollection extends BaseCollection
{
    public $_beforeRemove = false;
    public $_afterRemove = false;

    public function beforeRemove($event, $criteria)
    {
        $event->result['criteria'] = ['test' => false];
        $this->_beforeRemove = true;
    }

    public function afterRemove($event, $result, $criteria)
    {
        $this->_afterRemove = true;
    }

}