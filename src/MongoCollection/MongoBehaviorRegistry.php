<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/31/16
 * Time: 2:48 PM
 */

namespace CakeMonga\MongoCollection;


use Cake\ORM\BehaviorRegistry;

class MongoBehaviorRegistry extends BehaviorRegistry
{
    protected $_collection;

    public function __construct($collection = null)
    {
        if ($collection !== null) {
            $this->setCollection($collection);
        }
    }

    public function setCollection($collection)
    {
        $this->_collection = $collection;
        $this->eventManager($collection->eventManager());
    }

    protected function _create($class, $alias, $config)
    {
        $instance = new $class($this->_collection, $config);
        $enable = isset($config['enabled']) ? $config['enabled'] : true;
        if ($enable) {
            $this->eventManager()->on($instance);
        }
        $methods = $this->_getMethods($instance, $class, $alias);
        $this->_methodMap += $methods['methods'];
        $this->_finderMap += $methods['finders'];

        return $instance;
    }

}