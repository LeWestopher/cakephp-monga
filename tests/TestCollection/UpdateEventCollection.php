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

class UpdateEventCollection extends BaseCollection
{
    public $_beforeUpdate = false;
    public $_afterUpdate = false;

    public function beforeUpdate($event, $values, $query)
    {
        $event->result['values'] = ['test' => 50, 'check' => 3];
        $event->result['query'] = ['check' => 3];
        $this->_beforeUpdate = true;
    }

    public function afterUpdate($event, $entity)
    {
        $this->_afterUpdate = true;
    }

}