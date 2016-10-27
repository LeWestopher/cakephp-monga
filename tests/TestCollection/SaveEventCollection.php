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

class SaveEventCollection extends BaseCollection
{
    public $_beforeSave = false;
    public $_afterSave = false;

    public function beforeSave($event, $document)
    {
        $event->result['document'] = ['test' => false, 'check' => 1];
        $this->_beforeSave = true;
    }

    public function afterSave($event, $entity)
    {
        $this->_afterSave = true;
    }

}