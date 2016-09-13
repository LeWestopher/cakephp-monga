<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/23/16
 * Time: 9:22 PM
 */

namespace CakeMonga\Test\TestCase\MongoCollection;


use League\Monga\Collection;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;
use CakeMonga\MongoCollection\BaseCollection;
use Closure;
use Mockery as m;

class BaseCollectionTest extends TestCase
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

    public function testGetCollectionName()
    {
        $this->assertEquals('bases', $this->collection->getMongoCollectionName());
    }

    public function testSetConnection()
    {
        ConnectionManager::config('alt', [
            'className' => 'CakeMonga\Database\MongoConnection',
            'database' => 'local'
        ]);

        $conn = ConnectionManager::get('alt');
        $coll = new BaseCollection($conn);

        $coll->setConnection(ConnectionManager::get('testing'));
        $this->assertEquals('testing', $coll->getConnection()->configName());

    }

    public function testSetMaxRetries()
    {
        $this->collection->setMaxRetries(5);
        $reflection = new \ReflectionObject($this->collection->collection);
        $property = $reflection->getProperty('maxRetries');
        $property->setAccessible(true);
        $this->assertEquals(5, $property->getValue($this->collection->collection));
    }
    public function testCount()
    {
        $result = $this->collection->count();
        $this->assertEquals(0, $result);
        $this->collection->getCollection()->insert(['this' => 'value']);
        $result = $this->collection->count();
        $this->assertEquals(1, $result);
    }
    /**
     * @expectedException InvalidArgumentException
     */
    public function testCountException()
    {
        $this->collection->count(false);
    }

    public function testCountClosure()
    {
        $where = function ($query) {
            $query->where('name', 'Frank');
        };
        $result = $this->collection->count($where);
        $this->assertEquals(0, $result);
        $this->collection->getCollection()->insert(['name' => 'Frank']);
        $result = $this->collection->count($where);
        $this->assertEquals(1, $result);
    }
    public function testDistinct()
    {
        $collection = m::mock('MongoCollection');
        $collection->shouldReceive('distinct')
            ->with('surname', ['age' => 25])
            ->once()
            ->andReturn(['randomstring']);
        $expected = ['randomstring'];
        $c = new Collection($collection);
        $result = $c->distinct('surname', ['age' => 25]);
        $this->assertEquals($expected, $result);
    }

    public function testDistinctClosure()
    {
        $collection = m::mock('MongoCollection');
        $collection->shouldReceive('distinct')
            ->with('surname', ['age' => 25])
            ->once()
            ->andReturn(['randomstring']);
        $expected = ['randomstring'];
        $c = new Collection($collection);
        $result = $c->distinct('surname', function ($w) {
            $w->where('age', 25);
        });
        $this->assertEquals($expected, $result);
    }

    public function testAggregation()
    {
        $collection = m::mock('MongoCollection');
        $collection->shouldReceive('aggregate')
            ->with(['randomstring'])
            ->once()
            ->andReturn(['randomstring']);
        $expected = ['randomstring'];
        $c = new Collection($collection);
        $result = $c->aggregate(['randomstring']);
        $this->assertEquals($expected, $result);
    }

    public function testAggregationClosure()
    {
        $collection = m::mock('MongoCollection');
        $collection->shouldReceive('aggregate')
            ->with([
                ['$limit' => 1],
            ])
            ->once()
            ->andReturn(['randomstring']);
        $expected = ['randomstring'];
        $c = new Collection($collection);
        $result = $c->aggregate(function ($a) {
            $a->limit(1);
        });
        $this->assertEquals($expected, $result);
    }

    public function testIndexes()
    {
        $result = false;
        $callback = function () use (&$result) {
            $result = true;
        };
        $this->collection->indexes($callback);
        $this->assertTrue($result);
    }

    public function testDrop()
    {
        $result = $this->collection->drop();
        $this->assertFalse($result);
        $this->collection->insert(['name' => 'Frank']);
        $result = $this->collection->drop();
        $this->assertTrue($result);
    }
    public function testTruncate()
    {
        $result = $this->collection->truncate();
        $this->assertTrue($result);
    }
    public function testRemove()
    {
        $result = $this->collection->remove([]);
        $this->assertTrue($result);
    }
    public function testRemoveWhere()
    {
        $this->collection->getCollection()->insert(['name' => 'Frank']);
        $this->assertEquals(1, $this->collection->count());
        $result = $this->collection->remove(['name' => 'Bert']);
        $this->assertTrue($result);
        $this->assertEquals(1, $this->collection->count());
        $result = $this->collection->remove(['name' => 'Frank']);
        $this->assertTrue($result);
        $this->assertEquals(0, $this->collection->count());
    }
    public function testRemoveWhereClosure()
    {
        $closure = function ($query) {
            $query->where('name', 'Frank');
        };
        $closure2 = function ($query) {
            $query->where('name', 'Bert');
        };
        $this->collection->getCollection()->insert(['name' => 'Frank']);
        $this->assertEquals(1, $this->collection->count());
        $result = $this->collection->remove($closure2);
        $this->assertTrue($result);
        $this->assertEquals(1, $this->collection->count());
        $result = $this->collection->remove($closure);
        $this->assertTrue($result);
        $this->assertEquals(0, $this->collection->count());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidRemove()
    {
        $this->collection->remove(false);
    }

    public function testListIndexes()
    {
        $this->assertInternalType('array', $this->collection->listIndexes());
    }

    public function testFind()
    {
        $result = $this->collection->find();
    }

    public function testFindOneEmpty()
    {
        $result = $this->collection->findOne();
        $this->assertNull($result);
    }

    public function testFindOneNotEmpty()
    {
        $this->collection->insert(['some' => 'value']);
        $result = $this->collection->findOne();
        $this->assertInternalType('array', $result);
        $this->assertEquals('value', $result['some']);
    }

    public function testFindOneWithPostFindAction()
    {
        $result = $this->collection->findOne(function ($query) {
            $query->where('some', 'value')
                ->orderBy('some', 'asc')
                ->skip(0)
                ->limit(1);
        });
        $this->assertNull($result);
    }

    public function testFindOneWithPostFindActionWithResult()
    {
        $this->collection->insert(['some' => 'value']);
        $result = $this->collection->findOne(function ($query) {
            $query->where('some', 'value')
                ->orderBy('some', 'asc')
                ->skip(0)
                ->limit(1);
        });
        $this->assertInternalType('array', $result);
        $this->assertEquals('value', $result['some']);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidFind()
    {
        $this->collection->find(false);
    }

    public function testInsertOne()
    {
        $result = $this->collection->insert(['new' => 'entry']);
        $this->assertInstanceOf('MongoId', $result);
    }

    public function testInsertMultiple()
    {
        $result = $this->collection->insert([
            ['number' => 'one'],
            ['number' => 'two'],
        ]);
        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf('MongoId', $result);
    }

    public function testSave()
    {
        $item = ['name' => 'Frank'];
        $result = $this->collection->save($item);
        $this->assertTrue($result);
    }

    public function testUpdate()
    {
        $result = $this->collection->update(['name' => 'changed']);
        $this->assertTrue($result);
    }


    public function testUpdateClosure()
    {
        $result = $this->collection->update(function ($query) {
            $query->set('name', 'changed')
                ->increment('viewcount', 2);
        });
        $this->assertTrue($result);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidUpdate()
    {
        $result = $this->collection->update(false);
    }

    public function testGet()
    {
        $this->collection->insert(['alpha' => 'beta']);
        $result = $this->collection->findOne(['alpha' => 'beta']);
        $id = $result['_id'];
        $final = $this->collection->get($id);
        $this->assertEquals($id, $final['_id']);
    }
}