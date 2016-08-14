<?php

namespace CakeMonga\Test\TestCase\MongoCollection;
use Cake\TestSuite\TestCase;
use CakeMonga\MongoCollection\CollectionRegistry;

/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/13/16
 * Time: 2:02 PM
 */
class CollectionRegistryTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        CollectionRegistry::clear();
        CollectionRegistry::defaultNamespace();
        CollectionRegistry::setDefaultConnection('mongo_db');
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testSetNamespace()
    {
        CollectionRegistry::setNamespace("App\\Test\\Namespace\\");
        $this->assertEquals("App\\Test\\Namespace\\", CollectionRegistry::getNamespace());
    }

    public function testDefaultNamespace()
    {
        $this->assertEquals("App\\Model\\MongoCollection\\", CollectionRegistry::getNamespace());
    }

    public function testGet()
    {
        CollectionRegistry::setNamespace("CakeMonga\\Test\\TestCollection\\");
        $test_collection = CollectionRegistry::get('Tests');
        $this->assertEquals('hello', $test_collection->world());
    }

    public function testClear()
    {
        CollectionRegistry::setNamespace("CakeMonga\\Test\\TestCollection\\");
        $test_collection = CollectionRegistry::get('Tests');
        CollectionRegistry::clear();
        $this->assertEquals([], CollectionRegistry::getInstances());
    }

    public function testDefaultConnectionString()
    {
        $this->assertEquals('mongo_db', CollectionRegistry::getDefaultConnection());
    }

    public function setDefaultConnectionString()
    {
        CollectionRegistry::setDefaultConnection('new_default_connection');
        $this->assertEquals('new_default_connection', CollectionRegistry::getDefaultConnection());
    }
}