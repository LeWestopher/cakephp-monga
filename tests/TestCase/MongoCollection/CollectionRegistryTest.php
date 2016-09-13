<?php

namespace CakeMonga\Test\TestCase\MongoCollection;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;
use CakeMonga\MongoCollection\CollectionRegistry;

/**
 * Created by PhpStorm.
 * Author: Westopher King
 * Date: 8/13/16
 * Time: 2:02 PM
 */
class CollectionRegistryTest extends TestCase
{
    public static function setupBeforeClass()
    {
        ConnectionManager::drop('testing');
    }

    public function setUp()
    {
        parent::setUp();
        ConnectionManager::config('testing', [
            'className' => 'CakeMonga\Database\MongoConnection',
            'database' => 'local'
        ]);
        CollectionRegistry::clear();
        CollectionRegistry::defaultNamespace();
        CollectionRegistry::setDefaultConnection('mongo_db');
    }

    public function tearDown()
    {
        parent::tearDown();
        ConnectionManager::drop('testing');
    }

    public function testSetNamespace()
    {
        CollectionRegistry::setNamespace("App\\Test\\Namespace\\");
        $this->assertEquals("App\\Test\\Namespace\\", CollectionRegistry::getNamespace());
    }

    public function testGetNamespace()
    {
        $namespace = CollectionRegistry::getNamespace();
        $this->assertEquals("App\\Model\\MongoCollection\\", $namespace);
    }

    public function testGetInstances()
    {
        $this->assertEquals([], CollectionRegistry::getInstances());
    }

    public function testDefaultNamespace()
    {
        $this->assertEquals("App\\Model\\MongoCollection\\", CollectionRegistry::getNamespace());
    }

    public function testGet()
    {
        CollectionRegistry::setNamespace("CakeMonga\\Test\\TestCollection\\");
        $test_collection = CollectionRegistry::get('Tests', ['connection' => 'testing']);
        $this->assertEquals('hello', $test_collection->world());
    }

    public function testClear()
    {
        CollectionRegistry::setNamespace("CakeMonga\\Test\\TestCollection\\");
        $test_collection = CollectionRegistry::get('Tests', ['connection' => 'testing']);
        CollectionRegistry::clear();
        $this->assertEquals([], CollectionRegistry::getInstances());
    }

    public function testDefaultConnectionString()
    {
        $this->assertEquals('mongo_db', CollectionRegistry::getDefaultConnection());
    }

    public function testSetDefaultConnectionString()
    {
        CollectionRegistry::setDefaultConnection('new_default_connection');
        $this->assertEquals('new_default_connection', CollectionRegistry::getDefaultConnection());
    }

    public function testCustomConnectionConfig()
    {
        ConnectionManager::config('mongo_db', [
            'className' => 'CakeMonga\Database\MongoConnection',
            'database' => 'local'
        ]);

        CollectionRegistry::setNamespace("CakeMonga\\Test\\TestCollection\\");
        $test_collection = CollectionRegistry::get('Tests');
        $conn_name = $test_collection->getConnection()->configName();
        $this->assertEquals('mongo_db', $conn_name);

        ConnectionManager::drop('mongo_db');
    }
}