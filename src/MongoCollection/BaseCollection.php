<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/14/16
 * Time: 10:32 PM
 */

namespace CakeMonga\MongoCollection;


use Cake\Utility\Inflector;
use CakeMonga\Database\MongoConnection;

class BaseCollection
{
    protected $_connection;

    protected $_collection;

    public function __construct(MongoConnection $connection)
    {
        $this->setConnection($connection);
        $this->database = $connection->getDefaultDatabase();
        $collection_name = $this->getMongoCollectionName();
        $this->setMongaCollection($collection_name);
        return $this;
    }

    public function getConnection()
    {
        return $this->_connection;
    }

    public function setConnection(MongoConnection $connection)
    {
        $this->_connection = $connection;
        return $this->_connection;
    }

    public function getMongoCollectionName()
    {
        // Get full namespaced class name
        $class = get_class($this);
        $split_array = explode('\\', $class);
        // Split into namespaces and get the last namespace as the class name
        $final = $split_array[count($split_array) - 1];
        // Cut the final 10 characters off of the class (Collection from UsersCollection) and return just the collection
        return Inflector::tableize(substr($final, 0, -10));
    }

    public function setMongaCollection($collection_name)
    {
        $this->collection = $this->database->collection($collection_name);
        return $this->collection;
    }

    public function collection($collection = null)
    {
        if ($collection) {
            $this->_collection = $collection;
        }
        return $this->_collection;
    }

    public function getCollection()
    {
        return $this->collection;
    }

    public function find($query = [], $fields = [], $findOne = false)
    {
        return $this->collection->find($query, $fields, $findOne);
    }

    public function setMaxRetries($amount)
    {
        return $this->collection->setMaxRetries($amount);
    }

    public function drop()
    {
        return $this->collection->drop();
    }

    public function get($id, $fields = [])
    {
        return $this->collection->findOne([
            '_id' => $id
        ], $fields);
    }

    public function save(&$document, $options)
    {
        return $this->collection->save($document, $options);
    }

    public function update($values = [], $query = null, $options = [])
    {
        return $this->collection->update($values, $query, $options);
    }

    public function insert(array $data, $options = [])
    {
        return $this->collection->insert($data, $options);
    }

    public function remove($criteria, $options = [])
    {
        return $this->collection->remove($criteria, $options);
    }

    public function truncate()
    {
        return $this->collection->truncate();
    }

    public function aggregate($aggregation = [])
    {
        return $this->collection->aggregate($aggregation);
    }

    public function distinct($key, $query = [])
    {
        return $this->collection->distinct($key, $query);
    }

    public function count($query = [])
    {
        return $this->collection->count($query);
    }


}