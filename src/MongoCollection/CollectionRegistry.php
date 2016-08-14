<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/13/16
 * Time: 1:10 PM
 */

namespace CakeMonga\MongoCollection;


use Cake\Core\Exception\Exception;

class CollectionRegistry
{
    protected static $_instances = [];

    protected static $_instanceNamespace = "App\\Model\\MongoCollection\\";

    protected static $_defaultConnection = 'mongo_db';

    public static function get($alias, $config = [])
    {
        /*if (!static::exists($alias)) {
            throw new Exception('Mongo Collection instance not found!');
        }*/

        if (static::_isInstantiated($alias)) {
            return static::$_instances[$alias];
        }

        static::$_instances[$alias] = static::_create($alias);

        return static::$_instances[$alias];
    }

    protected static function _create($instance)
    {
        $class = static::$_instanceNamespace . $instance . "Collection";
        return new $class;
    }

    public static function exists($alias)
    {
        return isset(static::$_instances[$alias]);
    }

    public static function getNamespace()
    {
        return static::$_instanceNamespace;
    }

    public static function setNamespace($namespace)
    {
        static::$_instanceNamespace = $namespace;
        return static::$_instanceNamespace;
    }

    protected static function _isInstantiated($alias)
    {
        if (isset(static::$_instances[$alias])) {
            return (get_class(static::$_instances[$alias]) === 'CakeMonga\MongoCollection\Collection');
        }
        return false;
    }

    public static function clear()
    {
        static::$_instances = [];
    }

    public static function defaultNamespace()
    {
        static::$_instanceNamespace = "App\\Model\\MongoCollection\\";
    }

    public static function getInstances()
    {
        return static::$_instances;
    }

    public static function setDefaultConnection($connection)
    {
        static::$_defaultConnection = $connection;
        return static::$_defaultConnection;
    }

    public static function getDefaultConnection()
    {
        return static::$_defaultConnection;
    }


}