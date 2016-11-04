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
    /**
     * Holds a MongoCollection object via the setCollection() method
     *
     * @var
     */
    protected $_collection;

    /**
     * MongoBehaviorRegistry constructor.
     *
     * Functionally identical to the regular BehaviorRegistry constructor instead sets $this->_collection instead of
     * $this->_table to a Table object.
     *
     * @param null $collection
     */
    public function __construct($collection = null)
    {
        if ($collection !== null) {
            $this->setCollection($collection);
        }
    }
    /**
     * Sets the BaseCollection object for the registry and attaches the event manager from the collection to the current
     * event manager on the registry class.
     *
     * @param $collection
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;
        $this->eventManager($collection->eventManager());
    }

    /**
     * Overridden _create() method that injects a BaseCollection object into a MongoBehavior rather than a Behavior
     * object.
     *
     * @param string $class
     * @param string $alias
     * @param array $config
     * @return mixed
     */
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