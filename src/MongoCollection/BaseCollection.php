<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/14/16
 * Time: 10:32 PM
 */

namespace CakeMonga\MongoCollection;


use Cake\Event\EventDispatcherInterface;
use Cake\Event\EventDispatcherTrait;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Cake\Utility\Inflector;
use CakeMonga\Database\MongoConnection;
use Closure;

/**
 * Class BaseCollection
 * @package CakeMonga\MongoCollection
 * @author Wes King
 */
class BaseCollection implements EventListenerInterface, EventDispatcherInterface
{

    use EventDispatcherTrait;

    /**
     * Holds the current connection object that queries are made over.
     *
     * @var
     */
    protected $_connection;

    /**
     * Wraps the current instance of the MongaCollection object that queries are made against.
     * @var
     */
    protected $_collection;

    /**
     * BaseCollection constructor.
     * @param MongoConnection $connection
     */
    public function __construct(MongoConnection $connection)
    {
        $this->setConnection($connection);
        $this->database = $connection->getDefaultDatabase();
        $collection_name = $this->getMongoCollectionName();
        $this->setMongaCollection($collection_name);
        return $this;
    }

    public function implementedEvents()
    {
        $eventMap = [
            'Model.beforeFind' => 'beforeFind',
            'Model.beforeSave' => 'beforeSave',
            'Model.afterSave' => 'afterSave',
            'Model.beforeDelete' => 'beforeDelete',
            'Model.afterDelete' => 'afterDelete',
        ];
        $events = [];

        foreach ($eventMap as $event => $method) {
            if (!method_exists($this, $method)) {
                continue;
            }
            $events[$event] = $method;
        }

        return $events;
    }


    /**
     * Returns the current connection injected into this collection.
     *
     * @return mixed
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * Allows you to set a new Connection object onto the current Collection.  Note that if this collection has already
     * been instantiated, setting a new Connection object will not change the current Collection object that has been
     * set on `$this->_collection`.
     *
     * @param MongoConnection $connection
     * @return MongoConnection
     */
    public function setConnection(MongoConnection $connection)
    {
        $this->_connection = $connection;
        return $this->_connection;
    }

    /**
     * Infers the name of the Mongo collection inside of the database based on the namespace of the current class.
     *
     * @return string
     */
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

    /**
     * Sets a new collection property based on the lowercase, tableized collection name passed in as the first arg.
     *
     * @param string $collection_name
     * @return mixed
     */
    public function setMongaCollection($collection_name)
    {
        $this->collection = $this->database->collection($collection_name);
        return $this->collection;
    }
    /**
     * Returns the MongaCollection object.
     *
     * @return mixed
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Wraps Mongoa's native `find()` function on their Collection object.
     *
     * @param array $query
     * @param array $fields
     * @param bool $findOne
     * @return mixed
     */
    public function find($query = [], $fields = [], $findOne = false)
    {
        return $this->collection->find($query, $fields, $findOne);
    }

    /**
     * Wraps Monga's native `findOne()` method on their Collection object.
     *
     * @param array $query
     * @param array $fields
     * @return mixed
     */
    public function findOne($query = [], $fields = [])
    {
        return $this->collection->findOne($query, $fields);
    }

    /**
     * Wraps Monga's native `indexes()` method on their Collection object.
     *
     * @param Closure $callback
     * @return mixed
     */
    public function indexes(Closure $callback)
    {
        return $this->collection->indexes($callback);
    }

    /**
     * Wraps Monga's native `setMaxRetries()` method on their Collection object.
     *
     * @param int $amount
     * @return mixed
     */
    public function setMaxRetries($amount)
    {
        return $this->collection->setMaxRetries($amount);
    }

    /**
     * Wraps Monga's native `drop()` method on their Collection object.
     *
     * @return mixed
     */
    public function drop()
    {
        return $this->collection->drop();
    }

    /**
     * Wraps Monga's native `listIndexes()` method on their Collection object.
     *
     * @return mixed
     */
    public function listIndexes()
    {
        return $this->collection->listIndexes();
    }

    /**
     * Beginnings of a `get()` method to satisfy CakePHP's RepositoryInterface abstraction requirements.
     *
     * @param $id
     * @param array $fields
     * @return mixed
     */
    public function get($id, $fields = [])
    {
        return $this->collection->findOne([
            '_id' => $id
        ], $fields);
    }

    /**
     * Wraps Monga's native `save()` method on their Collection object.
     *
     * @param $document
     * @param array $options
     * @return mixed
     */
    public function save($document, $options = [])
    {
        $before_save_event = $this->dispatchEvent('Model.beforeSave', compact('document', 'options'));

        if ($before_save_event->isStopped()) {
            return $before_save_event->result;
        }

        $results = $this->collection->save($document, $options);

        $after_save_event = $this->dispatchEvent('Model.afterSave', compact('results', 'document', 'options'));

        return $results;
    }

    /**
     * Wraps Monga's native 'update()' method on their Collection object.
     *
     * @param array $values
     * @param null $query
     * @param array $options
     * @return mixed
     */
    public function update($values = [], $query = null, $options = [])
    {
        return $this->collection->update($values, $query, $options);
    }

    /**
     * Wraps Monga's native `insert()` method on their Collection object.
     *
     * @param array $data
     * @param array $options
     * @return mixed
     */
    public function insert(array $data, $options = [])
    {
        return $this->collection->insert($data, $options);
    }

    /**
     * Wraps Monga's native `remove()` method on their Collection object.
     *
     * @param $criteria
     * @param array $options
     * @return mixed
     */
    public function remove($criteria, $options = [])
    {
        return $this->collection->remove($criteria, $options);
    }

    /**
     * Wraps Monga's native `truncate()` method on their Collection object.
     *
     * @return mixed
     */
    public function truncate()
    {
        return $this->collection->truncate();
    }

    /**
     * Wraps Monga's native `aggregate()` method on their Collection object.
     *
     * @param array $aggregation
     * @return mixed
     */
    public function aggregate($aggregation = [])
    {
        return $this->collection->aggregate($aggregation);
    }

    /**
     * Wraps Monga's native `distinct()` method on their Collection object.
     *
     * @param $key
     * @param array $query
     * @return mixed
     */
    public function distinct($key, $query = [])
    {
        return $this->collection->distinct($key, $query);
    }

    /**
     * Wraps Monga's native `count()` method on their Collection object.
     *
     * @param array $query
     * @return mixed
     */
    public function count($query = [])
    {
        return $this->collection->count($query);
    }

    /**
     * Wraps Monga's native `setCollection()` method on their Collection object.
     *
     * @param \MongoCollection $collection
     * @return mixed
     */
    public function setCollection($collection)
    {
        return $this->collection->setCollection($collection);
    }
}