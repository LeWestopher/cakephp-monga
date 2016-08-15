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
        $class = get_class($this);
        $split_array = explode('\\', $class);
        $final = $split_array[count($split_array) - 1];
        return Inflector::tableize(substr($final, 0, -10));
    }

    public function setMongaCollection($collection_name)
    {
        $this->collection = $this->database->collection($collection_name);
        return $this->collection;
    }
}