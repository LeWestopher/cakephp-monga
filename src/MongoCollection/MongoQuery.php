<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/25/16
 * Time: 7:53 PM
 */

namespace CakeMonga\MongoCollection;


use Aura\Intl\Exception;
use Cake\Collection\Collection;
use Closure;

class MongoQuery
{
    protected $_whereArray = [];

    protected $_selectArray = [];

    protected $_hydrate = true;

    protected $_collection;

    protected $_entityName;

    protected $_defaultNamespace = 'App\\Model\\Entity';

    protected $_closure;

    protected $_findType = 'all';

    protected $_resultFormatter;

    public function __construct($collection, $entity_name, $config = [])
    {
        $this->_collection = $collection;
        $this->_entityName = $entity_name;

        if (isset($config['closure'])) {
            $this->closure($config['closure']);
        }

        if (isset($config['query'])) {
            $this->where($config['query']);
        }

        if (isset($config['fields'])) {
            $this->select($config['fields']);
        }

        if (isset($config['formatter'])) {
            $this->formatResults($config['formatter']);
        }

        if (isset($config['hydration'])) {
            $this->hydration($config['hydration']);
        }

        if (isset($config['entityNamespace'])) {
            $this->setDefaultEntityNamespace($config['entityNamespace']);
        }
    }

    public function select(array $fields = [])
    {
        foreach($fields as $field) {
            $this->_selectArray[$field] = true;
        }
        return $this;
    }

    public function excludeFields(array $fields = [])
    {
        foreach($fields as $field) {
            $this->_selectArray[$field] = false;
        }
        return $this;
    }

    public function where($params = [])
    {
        if ($params instanceof Closure) {
            return $this->closure($params);
        }
        $this->_whereArray = $this->_whereArray + $params;
        return $this;
    }

    public function closure(Closure $query)
    {
        $this->_closure = $query;
        return $this;
    }

    public function formatResults(Closure $formatter)
    {
        $this->_resultFormatter = $formatter;
        return $this;
    }

    public function all()
    {
        $function = [$this->_collection, 'find'];

        $args = isset($this->_closure)
            ? [$this->_closure, $this->_selectArray]
            : [$this->_whereArray, $this->_selectArray];

        $raw = call_user_func_array($function, $args);

        $results = $this->_hydrate ? $this->hydrateAll($raw->toArray()) : $raw->toArray();

        $collection = new Collection($results);

        if ($this->_resultFormatter) {
            $collection = $collection->map($this->_resultFormatter);
        }

        return $collection;
    }

    public function first()
    {
        $function = [$this->_collection, 'findOne'];

        $args = isset($this->_closure)
            ? [$this->_closure, $this->_selectArray]
            : [$this->_whereArray, $this->_selectArray];

        $raw = call_user_func_array($function, $args);

        $result = $this->_hydrate ? $this->hydrate($raw) : $raw;

        return $result;
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

    public function setDefaultEntityNamespace($namespace)
    {
        $this->_defaultNamespace = $namespace;
        return $this;
    }

    public function getEntityNamespace()
    {
        return $this->_defaultNamespace . '\\' . $this->_entityName;
    }
}