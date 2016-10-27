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

class StopEventCollection extends BaseCollection
{
    public $_eventTrigger;

    public function world()
    {
        return 'hello';
    }

    public function initialize($config = [])
    {
        $this->_eventTrigger = $config['stop_event'];
    }

    public function beforeFind($event, $query, $fields, $findOne)
    {
        if ($this->_eventTrigger === 'find') {
            $event->stopPropagation();
        }
    }

    public function beforeSave($event, $document)
    {
        if ($this->_eventTrigger === 'save') {
            $event->stopPropagation();
        }
    }

    public function beforeInsert($event, $data)
    {
        if ($this->_eventTrigger === 'insert') {
            $event->stopPropagation();
        }
    }

    public function beforeUpdate($event, $values, $query)
    {
        if ($this->_eventTrigger === 'update') {
            $event->stopPropagation();
        }
    }

    public function beforeRemove($event, $criteria)
    {
        if ($this->_eventTrigger === 'remove') {
            $event->stopPropagation();
        }
    }

}