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
    protected $_collection;

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