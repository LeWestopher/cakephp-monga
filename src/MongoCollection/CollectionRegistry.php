<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/13/16
 * Time: 1:10 PM
 */

namespace CakeMonga\MongoCollection;


use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;

/**
 * Serves as a singleton Registry instance for retrieving Collection classes from our `app/Model/MongoCollection` folder
 * quickly and efficiently.
 *
 * Class CollectionRegistry
 * @package CakeMonga\MongoCollection
 * @author Wes King
 */
class CollectionRegistry
{
    /**
     * Array containing a list of all previously built Collection object instances.
     *
     * @var array
     */
    protected static $_instances = [];

    /**
     * The default namespace for where your *Collection.php classes should be located.  This can be changed using
     * `CollectionRegistry::setNamespace('your\\namespace\\here');`
     *
     * @var string
     */
    protected static $_instanceNamespace = "App\\Model\\MongoCollection\\";

    /**
     * The default connection datasource to inject into Collection instances.  This datasource is set in your app.php
     * file.
     *
     * @var string
     */
    protected static $_defaultConnection = 'mongo_db';

    /**
     * Get an instance of a Collection class from the default namespace with the appropriate datasource injected into
     * the Collection instance.  You can change the datasource of the returned Collection object by passing a datasource
     * string into the 'connection' paramater of the $config array for this function.
     *
     * @param $alias
     * @param array $config
     * @return mixed
     */
    public static function get($alias, $config = [])
    {
        if (static::_isInstantiated($alias)) {
            return static::$_instances[$alias];
        }

        // Sets connection based on whether
        $conn = isset($config['connection']) ? $config['connection'] : 'mongo_db';

        $mongo_connection = ConnectionManager::get($conn);

        static::$_instances[$alias] = static::_create($alias, $mongo_connection);

        return static::$_instances[$alias];
    }

    /**
     * Instantiates the appropriate Collection class with the appropriate Connection object injected into the
     * constructor.
     *
     * @param $instance
     * @param $connection
     * @return mixed
     */
    protected static function _create($instance, $connection)
    {
        $class = static::$_instanceNamespace . $instance . "Collection";
        return new $class($connection);
    }

    /**
     * Returns the namespace of where Collection classes are currently stored.
     *
     * @return string
     */
    public static function getNamespace()
    {
        return static::$_instanceNamespace;
    }

    /**
     * Sets the default namespace of where Collection classes should be sourced from by the registry.
     *
     * @param $namespace
     * @return string
     */
    public static function setNamespace($namespace)
    {
        static::$_instanceNamespace = $namespace;
        return static::$_instanceNamespace;
    }

    /**
     * Returns whether or not the appropriate alias has been instantiated by the registry or not.  Used for caching
     * instances inside of the registry class.
     *
     * @param $alias
     * @return bool
     */
    protected static function _isInstantiated($alias)
    {
        if (isset(static::$_instances[$alias])) {
            return (get_class(static::$_instances[$alias]) === 'CakeMonga\MongoCollection\Collection');
        }
        return false;
    }

    /**
     * Clears all current instances stored in the CollectionRegistry.
     */
    public static function clear()
    {
        static::$_instances = [];
    }

    /**
     * Resets the namespace of where Collection classes are sourced from back to the default namespace.
     */
    public static function defaultNamespace()
    {
        static::$_instanceNamespace = "App\\Model\\MongoCollection\\";
    }

    /**
     * Returns a list of all instances currently cached inside of the CollectionRegistry.
     *
     * @return array
     */
    public static function getInstances()
    {
        return static::$_instances;
    }

    /**
     * Changes the default connection string that gets injected into Collection constructors.
     *
     * @param $connection
     * @return string
     */
    public static function setDefaultConnection($connection)
    {
        static::$_defaultConnection = $connection;
        return static::$_defaultConnection;
    }

    /**
     * Returns the default connection string that is being injected into Collection constructors.
     *
     * @return string
     */
    public static function getDefaultConnection()
    {
        return static::$_defaultConnection;
    }
}