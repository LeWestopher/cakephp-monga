<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 9/14/16
 * Time: 10:34 AM
 */

namespace CakeMonga\Test\TestCase\MongoCollection;


use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;
use CakeMonga\MongoCollection\BaseCollection;
use CakeMonga\MongoCollection\MongoQuery;

class MongoQueryTest extends TestCase
{
    public static function setupBeforeClass()
    {
        ConnectionManager::config('testing', [
            'className' => 'CakeMonga\Database\MongoConnection',
            'database' => 'local'
        ]);
    }

    public function setUp()
    {
        parent::setUp();
        $connection = ConnectionManager::get('testing');
        $this->collection = new BaseCollection($connection);
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->collection->drop();
    }

    public function testSelectNoHydrate()
    {
        $this->collection->insert(['name' => 'test', 'check' => 'two']);
        $query = new MongoQuery($this->collection, 'Test', [
            'entityNamespace' => 'CakeMonga\\Test\\TestEntity',
            'hydration' => false
        ]);
        $result = $query->select(['name'])->first();
        $this->assertEquals('test', $result['name']);
    }

    public function testSelectWithHydrate()
    {
        $this->collection->insert(['name' => 'test', 'check' => 'two']);
        $query = new MongoQuery($this->collection, 'Test', [
            'entityNamespace' => 'CakeMonga\\Test\\TestEntity'
        ]);
        $result = $query->select(['name'])->first();
        $this->assertEquals('test', $result->name);
    }

    public function testWhereNoHydrate()
    {
        $this->collection->insert([
            ['order' => 1, 'name' => 'test'],
            ['order' => 2, 'name' => 'test'],
            ['order' => 3, 'name' => 'alt']
        ]);
        $query = new MongoQuery($this->collection->getCollection(), 'Test', [
            'entityNamespace' => 'CakeMonga\\Test\\TestEntity',
            'hydration' => false
        ]);
        $result = $query->where(['name' => 'test'])->all();
        $this->assertEquals(2, count($result->toArray()));
    }

    public function testWhereWithHydrate()
    {
        $this->collection->insert([
            ['order' => 1, 'name' => 'test'],
            ['order' => 2, 'name' => 'test'],
            ['order' => 3, 'name' => 'alt']
        ]);
        $query = new MongoQuery($this->collection->getCollection(), 'Test', [
            'entityNamespace' => 'CakeMonga\\Test\\TestEntity',
        ]);
        $result = $query->where(['name' => 'test'])->all();
        $this->assertEquals(2, count($result->toArray()));
        $this->assertEquals('test', $result->first()->name);
    }

    public function testFormatResultsNoHydrate()
    {
        $this->collection->insert([
            ['order' => 1, 'name' => 'test'],
            ['order' => 2, 'name' => 'test'],
            ['order' => 3, 'name' => 'alt']
        ]);
        $query = new MongoQuery($this->collection->getCollection(), 'Test', [
            'entityNamespace' => 'CakeMonga\\Test\\TestEntity',
            'hydration' => false
        ]);
        $result = $query->formatResults(function ($row) {
            $row['formatted'] = true;
            return $row;
        })->all();
        $this->assertEquals(true, $result->first()['formatted']);
    }

    public function testWhereWithClosure()
    {
        $this->collection->insert([
            ['order' => 1, 'name' => 'test'],
            ['order' => 2, 'name' => 'test'],
            ['order' => 3, 'name' => 'alt']
        ]);
        $query = new MongoQuery($this->collection->getCollection(), 'Test', [
            'entityNamespace' => 'CakeMonga\\Test\\TestEntity',
        ]);
        $result = $query->where(function ($query) {
            $query->where('name', 'test');
        })->all();
        $this->assertEquals(2, count($result->toArray()));
    }

    public function testWhereWithClosureAndSelect()
    {
        $this->collection->insert([
            ['order' => 1, 'name' => 'test'],
            ['order' => 2, 'name' => 'test'],
            ['order' => 3, 'name' => 'alt']
        ]);

        $query = new MongoQuery($this->collection->getCollection(), 'Test', [
            'entityNamespace' => 'CakeMonga\\Test\\TestEntity',
        ]);

        $result = $query->select(['name'])
            ->where(function ($query) {
                $query->where('name', 'test');
            })->all();

        $this->assertEquals(false, isset($result->first()->order));
    }

    public function testExcludeFields()
    {
        $this->collection->insert([
            ['order' => 1, 'name' => 'test'],
            ['order' => 2, 'name' => 'test'],
            ['order' => 3, 'name' => 'alt']
        ]);

        $query = new MongoQuery($this->collection->getCollection(), 'Test', [
            'entityNamespace' => 'CakeMonga\\Test\\TestEntity',
        ]);

        $result = $query->excludeFields(['order'])->first();

        $this->assertEquals(false, isset($result->order));
    }

    public function testConstructorWithClosure()
    {
        $this->collection->insert([
            ['order' => 1, 'name' => 'test'],
            ['order' => 2, 'name' => 'test'],
            ['order' => 3, 'name' => 'alt']
        ]);

        $closure = function ($query) {
            return $query->where('name', 'test');
        };

        $query = new MongoQuery($this->collection->getCollection(), 'Test', [
            'entityNamespace' => 'CakeMonga\\Test\\TestEntity',
            'closure' => $closure
        ]);

        $result = $query->all();

        $this->assertEquals(2, count($result->toArray()));
    }

    public function testConstructorWithQuery()
    {
        $this->collection->insert([
            ['order' => 1, 'name' => 'test'],
            ['order' => 2, 'name' => 'test'],
            ['order' => 3, 'name' => 'alt']
        ]);

        $where = ['name' => 'test'];

        $query = new MongoQuery($this->collection->getCollection(), 'Test', [
            'entityNamespace' => 'CakeMonga\\Test\\TestEntity',
            'query' => $where
        ]);

        $result = $query->all();

        $this->assertEquals(2, count($result->toArray()));
    }

    public function testConstructorWithSelect()
    {
        $this->collection->insert([
            ['order' => 1, 'name' => 'test'],
            ['order' => 2, 'name' => 'test'],
            ['order' => 3, 'name' => 'alt']
        ]);

        $select = ['name'];

        $query = new MongoQuery($this->collection->getCollection(), 'Test', [
            'entityNamespace' => 'CakeMonga\\Test\\TestEntity',
            'fields' => $select
        ]);

        $result = $query->first();

        $this->assertEquals(false, isset($result->order));
    }

    public function testConstructorWithExcludedFields()
    {
        $this->collection->insert([
            ['order' => 1, 'name' => 'test'],
            ['order' => 2, 'name' => 'test'],
            ['order' => 3, 'name' => 'alt']
        ]);

        $exclude = ['order'];

        $query = new MongoQuery($this->collection->getCollection(), 'Test', [
            'entityNamespace' => 'CakeMonga\\Test\\TestEntity',
            'excluded' => $exclude
        ]);

        $result = $query->first();

        $this->assertEquals(false, isset($result->order));
    }

    public function testConstructorWithFormatter()
    {
        $this->collection->insert([
            ['order' => 1, 'name' => 'test'],
            ['order' => 2, 'name' => 'test'],
            ['order' => 3, 'name' => 'alt']
        ]);

        $formatter = function ($row) {
            $row->formatted = true;
            return $row;
        };

        $query = new MongoQuery($this->collection->getCollection(), 'Test', [
            'entityNamespace' => 'CakeMonga\\Test\\TestEntity',
            'formatter' => $formatter
        ]);

        $result = $query->all();

        $this->assertEquals(true, count($result->first()->formatted));
    }
}