<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/31/16
 * Time: 3:08 PM
 */

namespace CakeMonga\MongoCollection;


use Cake\ORM\Behavior;

class MongoBehavior extends Behavior
{
    /**
     * Holds the Mongo BaseCollection object for the behavior.
     *
     * @var BaseCollection
     */
    protected $_collection;

    /**
     * MongoBehavior constructor.
     *
     * Sets the $this->_collection property to a BaseCollection instead of the traditional Behavior constructor
     * that sets $this->_table to a Table object.
     *
     * @param BaseCollection $collection
     * @param array $config
     */
    public function __construct(BaseCollection $collection, array $config = [])
    {
        $config = $this->_resolveMethodAliases(
            'implementedFinders',
            $this->_defaultConfig,
            $config
        );
        $config = $this->_resolveMethodAliases(
            'implementedMethods',
            $this->_defaultConfig,
            $config
        );
        $this->_collection = $collection;
        $this->config($config);
        $this->initialize($config);
    }
}