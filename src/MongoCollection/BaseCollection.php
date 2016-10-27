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
     * @param $config
     */
    public function __construct(MongoConnection $connection, $config = [])
    {
        $this->setConnection($connection);
        $this->database = $connection->getDefaultDatabase();
        $eventManager = $collection_name = null;

        if (!empty($config['eventManager'])) {
            $eventManager = $config['eventManager'];
        }

        if (!empty($config['collection'])) {
            $collection_name = $config['collection'];
        } else {
            $collection_name = $this->getMongoCollectionName();
        }

        $this->_eventManager = $eventManager ?: new EventManager();
        $this->_eventManager->on($this);
        $this->setMongaCollection($collection_name);
        $this->initialize($config);
        return $this;
    }

    /**
     * Initialize a Collection instance.  Called after the constructor.  Allows you to define any extra parameters
     * on the collection once it's been constructed.
     *
     * @param array $config
     */
    public function initialize($config = [])
    {

    }

    /**
     * Defines a list of events implemented on the Collection class for usage by the Collection's EventManager.
     *
     * @return array
     */
    public function implementedEvents()
    {
        $eventMap = [
            'Model.beforeFind' => 'beforeFind', // Done
            'Model.beforeSave' => 'beforeSave', // Done
            'Model.afterSave' => 'afterSave', // Done
            'Model.beforeInsert' => 'beforeInsert',
            'Model.afterInsert' => 'afterInsert',
            'Model.beforeUpdate' => 'beforeUpdate',
            'Model.afterUpdate' => 'afterUpdate',
            'Model.beforeRemove' => 'beforeRemove',
            'Model.afterRemove' => 'afterRemove',
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
     * If the beforeFind() method is defined, calls that method with this methods arguments and allows direct
     * modification of the $query and $fields arguments before the find query is called.
     *
     * @param array $query
     * @param array $fields
     * @param bool $findOne
     * @return mixed
     */
    public function find($query = [], $fields = [], $findOne = false)
    {
        $before_find_event = $this->dispatchEvent('Model.beforeFind', compact('query', 'fields', 'findOne'));

        if (!empty($before_find_event->result['query']) && $before_find_event->result['query'] !== $query) {
            $query = $before_find_event->result['query'];
        }

        if (!empty($before_find_event->result['fields']) && $before_find_event->result['fields'] !== $fields) {
            $fields = $before_find_event->result['fields'];
        }

        if ($before_find_event->isStopped()) {
            return false;
        }

        return $this->collection->find($query, $fields, $findOne);
    }

    /**
     * Wraps Monga's native `findOne()` method on their Collection object.
     *
     * If the beforeFind() method is defined, calls that method with this methods arguments and allows direct
     * modification of the $query and $fields arguments before the find query is called.
     *
     * @param array $query
     * @param array $fields
     * @return mixed
     */
    public function findOne($query = [], $fields = [])
    {
        $findOne = true;
        $before_find_event = $this->dispatchEvent('Model.beforeFind', compact('query', 'fields', 'findOne'));

        if (!empty($before_find_event->result['query']) && $before_find_event->result['query'] !== $query) {
            $query = $before_find_event->result['query'];
        }

        if (!empty($before_find_event->result['fields']) && $before_find_event->result['fields'] !== $fields) {
            $fields = $before_find_event->result['fields'];
        }

        if ($before_find_event->isStopped()) {
            return false;
        }

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
     * If the beforeSave() method is defined, calls that method with this methods arguments and allows direct
     * modification of the $document to be saved before the save is committed to the MongoDB instance.
     *
     * If the afterSave() method is defined, it is called after the save is successfully committed to the database.
     *
     * @param $document
     * @param array $options
     * @return mixed
     */
    public function save($document, $options = [])
    {
        $before_save_event = $this->dispatchEvent('Model.beforeSave', compact('document', 'options'));

        if (!empty($before_save_event->result['document']) && $before_save_event->result['document'] !== $document) {
            $document = $before_save_event->result['document'];
        }

        if ($before_save_event->isStopped()) {
            return false;
        }

        $results = $this->collection->save($document, $options);

        $after_save_event = $this->dispatchEvent('Model.afterSave', compact('results', 'document', 'options'));

        return $results;
    }

    /**
     * Wraps Monga's native 'update()' method on their Collection object.
     *
     * If the beforeUpdate() method is defined, calls that method with this methods arguments and allows direct
     * modification of the $values and $query arguments before the update query is called.
     *
     * If the afterUpdate() method is defined, it is called after the update is successfully committed to the database.
     *
     * @param array $values
     * @param null $query
     * @param array $options
     * @return mixed
     */
    public function update($values = [], $query = null, $options = [])
    {
        $before_update_event = $this->dispatchEvent('Model.beforeUpdate', compact('values', 'query'));

        if (!empty($before_update_event->result['values']) && $before_update_event->result['values'] !== $values) {
            $values = $before_update_event->result['values'];
        }

        if (!empty($before_update_event->result['query']) && $before_update_event->result['query'] !== $query) {
            $query = $before_update_event->result['query'];
        }

        if ($before_update_event->isStopped()) {
            return false;
        }

        $results = $this->collection->update($values, $query, $options);

        $after_update_event = $this->dispatchEvent('Model.afterUpdate', compact('results', 'query', 'values'));

        return $results;
    }

    /**
     * Wraps Monga's native `insert()` method on their Collection object.
     *
     * If the beforeInsert() method is defined, calls that method with this methods arguments and allows direct
     * modification of the $data argument before the insert query is called.
     *
     * If the afterInsert() method is defined, it is called after the insert is successfully committed to the database.
     *
     * @param array $data
     * @param array $options
     * @return mixed
     */
    public function insert(array $data, $options = [])
    {
        $before_insert_event = $this->dispatchEvent('Model.beforeInsert', compact('data', 'options'));

        if (!empty($before_insert_event->result['data']) && $before_insert_event->result['data'] !== $data) {
            $data = $before_insert_event->result['data'];
        }

        if ($before_insert_event->isStopped()) {
            return false;
        }

        $results = $this->collection->insert($data, $options);

        $after_insert_event = $this->dispatchEvent('Model.afterInsert', compact('results', 'data', 'options'));

        return $results;
    }

    /**
     * Wraps Monga's native `remove()` method on their Collection object.
     *
     * If the beforeRemove() method is defined, calls that method with this methods arguments and allows direct
     * modification of the $criteria argument before the remove query is called.
     *
     * If the afterRemove() method is defined, it is called after the remove is successfully committed to the database.
     *
     * @param $criteria
     * @param array $options
     * @return mixed
     */
    public function remove($criteria, $options = [])
    {
        $before_remove_event = $this->dispatchEvent('Model.beforeRemove', compact('criteria'));

        if (!empty($before_remove_event->result['criteria']) && $before_remove_event->result['criteria'] !== $criteria) {
            $criteria = $before_remove_event->result['criteria'];
        }

        if ($before_remove_event->isStopped()) {
            return false;
        }

        $result = $this->collection->remove($criteria, $options);

        $after_insert_event = $this->dispatchEvent('Model.afterRemove', compact('result', 'criteria'));

        return $result;
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