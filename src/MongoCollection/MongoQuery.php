<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/25/16
 * Time: 7:53 PM
 */

namespace CakeMonga\MongoCollection;


use Aura\Intl\Exception;

class MongoQuery
{
    protected $_whereArray = [];

    protected $_selectArray = [];

    protected $_hydrate = true;

    protected $_collection;

    protected $_entityName;

    protected $_defaultNamespace = 'App\\Model\\Entity';

    public function __construct($collection, $entity_name)
    {
        $this->_collection = $collection;
    }

    public function select(array $fields = []) {
        $this->_selectArray = array_merge($this->_selectArray, $fields);
        return $this;
    }

    public function where(array $params = [])
    {
        $this->_whereArray = $this->_whereArray + $params;
        return $this;
    }

    public function all()
    {
        return $this->callFinder('all');
    }

    public function first()
    {
        return $this->callFinder('first');
    }

    public function callFinder($find_type)
    {
        if ($find_type === 'all') {
            $raw = $this->_collection->find(
                $this->_whereArray,
                $this->_selectArray
            );
            $results = ($this->_hydrate) ? $this->hydrateAll($raw->toArray()) : $raw->toArray();
        } elseif ($find_type = 'first') {
            $raw = $this->_collection->findOne(
                $this->_whereArray,
                $this->_selectArray
            );
            $results = ($this->_hydrate) ? $this->hydrate($raw) : $raw;
        } else {
            throw new Exception(__(sprintf('Find type %s is not currently supported.', $find_type)));
        }

        return $results;

    }

    public function hydration($status) {
        $this->_hydrate = (boolean) $status;
        return $this->_hydrate;
    }

    public function hydrate($result)
    {
        $namespace = $this->getEntityNamespace();
        return new $namespace($result);
    }

    public function hydrateAll($results)
    {
        $hydrated = [];
        foreach($results as $result) {
            $hydrated[] = $this->hydrate($result);
        }
        return $hydrated;
    }

    public function getEntityNamespace()
    {
        return $this->_defaultNamespace . '\\' . $this->_entityName;
    }
}