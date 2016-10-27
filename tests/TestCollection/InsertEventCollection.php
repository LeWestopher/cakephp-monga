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

class InsertEventCollection extends BaseCollection
{
    public $_beforeInsert = false;
    public $_afterInsert = false;

    public function beforeInsert($event, $data)
    {
        $event->result['data'] = ['test' => true, 'excluded' => false];
        $this->_beforeInsert = true;
    }

    public function afterInsert($event, $results)
    {
        $this->_afterInsert = true;
    }
}