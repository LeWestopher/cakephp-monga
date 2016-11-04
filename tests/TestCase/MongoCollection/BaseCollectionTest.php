<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 8/23/16
 * Time: 9:22 PM
 */

namespace CakeMonga\Test\TestCase\MongoCollection;


use CakeMonga\MongoCollection\MongoBehaviorRegistry;
use CakeMonga\Test\TestCollection\DeleteEventCollection;
use CakeMonga\Test\TestCollection\FindEventCollection;
use CakeMonga\Test\TestCollection\InsertEventCollection;
use CakeMonga\Test\TestCollection\SaveEventCollection;
use CakeMonga\Test\TestCollection\StopEventCollection;
use CakeMonga\Test\TestCollection\TestsCollection;
use CakeMonga\Test\TestCollection\UpdateEventCollection;
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
        $this->database = $connection->connect()->database('__unit_testing__');
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->collection->drop();
        $this->database = null;
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
    /**
     * @test
     * @covers MongoCollection::distinct()
     */
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

    public function testDistinctQuery()
    {
        $connection = ConnectionManager::get('testing');
        $collection = new BaseCollection($connection);
        $collection->insert([
            ['test' => true, 'check' => 'a'],
            ['test' => true, 'check' => 'a'],
            ['test' => false, 'check' => 'b']
        ]);
        $results = $collection->distinct('check');
        $this->assertEquals(2, count($results));
    }

    /**
     * @covers MongoCollection::distinct()
     */
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
    /**
     * @test
     * @covers MongoCollection::aggregate()
     */
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

    /**
     * @covers MongoCollection::aggregate()
     */
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

    public function testSetCollection()
    {
        $original = $this->collection->getCollection()->getCollection();
        $originalHash = spl_object_hash($original);
        $new = $this->database->collection('__different__')->getCollection();
        $newHash = spl_object_hash($new);
        $this->collection->setCollection($new);
        $reflection = new \ReflectionObject($this->collection->getCollection());
        $property = $reflection->getProperty('collection');
        $property->setAccessible(true);
        $this->assertInstanceOf('MongoCollection', $property->getValue($this->collection->getCollection()));
        $this->assertEquals($newHash, spl_object_hash($property->getValue($this->collection->getCollection())));
        $this->assertNotEquals($originalHash, spl_object_hash($property->getValue($this->collection)));
        $this->collection->setCollection($original);
    }

    public function testBeforeSave()
    {
        $connection = ConnectionManager::get('testing');
        $collection = new SaveEventCollection($connection);
        $results = $collection->save(['test' => true]);
        $this->assertTrue($collection->_beforeSave);
        $collection->truncate();
    }

    public function testBeforeSaveDocumentAlter()
    {
        $connection = ConnectionManager::get('testing');
        $collection = new SaveEventCollection($connection);
        $collection->save(['test' => true, 'check' => '1']);
        // Testing beforeSave by modifying $document's 'test' key to equal false instead
        $result = $collection->findOne(['test' => false]);
        $this->assertEquals(1, $result['check']);
        $collection->truncate();
    }

    public function testAfterSave()
    {
        $connection = ConnectionManager::get('testing');
        $collection = new SaveEventCollection($connection);
        $collection->save(['test' => true]);
        $this->assertTrue($collection->_afterSave);
        $collection->truncate();
    }

    public function testBeforeFind()
    {
        $connection = ConnectionManager::get('testing');
        $collection = new FindEventCollection($connection);
        $collection->save(['test' => true]);
        $results = $collection->find(['test' => true]);
        $this->assertTrue($collection->_beforeFind);
        $collection->truncate();
    }

    public function testBeforeFindQueryAlter()
    {
        $connection = ConnectionManager::get('testing');
        $collection = new FindEventCollection($connection);
        $collection->insert([
            ['test' => true],
            ['test' => false]
        ]);
        // TestsCollection.php users beforeFind() to modify the $query array to search for ['test' => false] instead
        $results = $collection->findOne(['test' => true]);
        $this->assertFalse($results['test']);
        $collection->truncate();
    }

    public function testBeforeFindFieldsAlter()
    {
        $connection = ConnectionManager::get('testing');
        $collection = new FindEventCollection($connection);
        $collection->insert([
            ['test' => true, 'excluded' => true]
        ]);
        $results = $collection->findOne(['test' => true], ['test', 'excluded']);
        // TestsCollection.php uses beforeFind() to modify the $fields array to only include the 'test' field
        $this->assertFalse(isset($results['excluded']));
        $collection->truncate();
    }

    public function testBeforeInsertDocumentAlter()
    {
        $connection = ConnectionManager::get('testing');
        $collection = new InsertEventCollection($connection);
        $collection->insert([
            ['test' => true, 'excluded' => true]
        ]);
        $results = $collection->findOne(['test' => true]);
        // TestsCollection.php uses beforeFind() to modify the $fields array to only include the 'test' field
        $this->assertFalse($results['excluded']);
        $collection->truncate();
    }

    public function testBeforeAndAfterUpdate()
    {
        $connection = ConnectionManager::get('testing');
        $collection = new UpdateEventCollection($connection);
        $collection->insert([
            ['test' => true, 'check' => 1],
            ['test' => true, 'check' => 2]
        ]);
        $collection->update(['test' => false], ['check' => 2]);
        // Testing beforeSave by modifying $document's 'test' key to equal false instead
        $this->assertTrue($collection->_beforeUpdate);
        $this->assertTrue($collection->_afterUpdate);
        $collection->truncate();
    }

    public function testBeforeUpdateDataAlter()
    {
        $connection = ConnectionManager::get('testing');
        $collection = new UpdateEventCollection($connection);
        $collection->insert([
            ['test' => true, 'check' => 1],
            ['test' => true, 'check' => 2],
            ['test' => true, 'check' => 3]
        ]);
        $collection->update(['test' => false], ['check' => 2]);
        $result = $collection->findOne(['check' => 3]);
        // Testing beforeSave by modifying $document's 'test' key to equal false instead
        $this->assertEquals(50, $result['test']);
        $collection->truncate();
    }

    public function testBeforeAndAfterRemove()
    {
        $connection = ConnectionManager::get('testing');
        $collection = new DeleteEventCollection($connection);
        $collection->insert([
            ['test' => true, 'check' => 1],
            ['test' => true, 'check' => 2],
            ['test' => true, 'check' => 3]
        ]);
        $collection->remove(['test' => true]);
        $this->assertTrue($collection->_beforeRemove);
        $this->assertTrue($collection->_afterRemove);
        $collection->truncate();
    }

    public function testBeforeRemoveCriteriaAlter()
    {
        $connection = ConnectionManager::get('testing');
        $collection = new DeleteEventCollection($connection);
        $collection->insert([
            ['test' => true, 'check' => 1],
            ['test' => true, 'check' => 2],
            ['test' => false, 'check' => 3]
        ]);
        $collection->remove(['test' => true]);
        $this->assertEquals(2, $collection->count());
    }

    public function testBeforeFindStop()
    {
        $connection = ConnectionManager::get('testing');
        $collection = new StopEventCollection($connection, ['stop_event' => 'find']);
        $collection->insert([
            ['test' => true, 'check' => 1],
            ['test' => true, 'check' => 2],
            ['test' => false, 'check' => 3]
        ]);
        $results = $collection->find();
        $this->assertFalse($results);
        $collection->truncate();
    }

    public function testBeforeFindOneStop()
    {
        $connection = ConnectionManager::get('testing');
        $collection = new StopEventCollection($connection, ['stop_event' => 'find']);
        $collection->insert([
            ['test' => true, 'check' => 1],
            ['test' => true, 'check' => 2],
            ['test' => false, 'check' => 3]
        ]);
        $results = $collection->findOne();
        $this->assertFalse($results);
        $collection->truncate();
    }

    public function testBeforeSaveStop()
    {
        $connection = ConnectionManager::get('testing');
        $collection = new StopEventCollection($connection, ['stop_event' => 'save']);
        $results = $collection->save(['test' => true]);
        $this->assertEquals(0, $collection->count());
        $this->assertFalse($results);
        $collection->truncate();
    }

    public function testBeforeInsertStop()
    {
        $connection = ConnectionManager::get('testing');
        $collection = new StopEventCollection($connection, ['stop_event' => 'insert']);
        $results = $collection->insert(['test' => true]);
        $this->assertEquals(0, $collection->count());
        $this->assertFalse($results);
        $collection->truncate();
    }

    public function testBeforeDeleteStop()
    {
        $connection = ConnectionManager::get('testing');
        $collection = new StopEventCollection($connection, ['stop_event' => 'remove']);
        $results = $collection->insert(['test' => true]);
        $collection->remove(['test' => true]);
        $this->assertEquals(1, $collection->count());
        $collection->truncate();
    }

    public function testBeforeUpdateStop()
    {
        $connection = ConnectionManager::get('testing');
        $collection = new StopEventCollection($connection, ['stop_event' => 'update']);
        $results = $collection->insert(['test' => true]);
        $collection->update(['test' => false]);
        $result = $collection->findOne(['test' => true]);
        $this->assertTrue($result['test']);
        $collection->truncate();
    }

    public function testHasBehaviors()
    {
        $connection = ConnectionManager::get('testing');
        $collection = new BaseCollection($connection);
        $this->assertEquals('CakeMonga\MongoCollection\MongoBehaviorRegistry', get_class($collection->behaviors()));
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testThrowsBadMethodException()
    {
        $connection = ConnectionManager::get('testing');
        $collection = new BaseCollection($connection);
        $collection->findThisMethodDoesntExist();
    }

    public function testAddBehavior()
    {
        $connection = ConnectionManager::get('testing');
        $collection = new BaseCollection($connection);
        $collection->addBehavior('CakeMonga\Test\TestCollection\TestBehavior');
        $this->assertTrue($collection->hasBehavior('CakeMonga\Test\TestCollection\TestBehavior'));
    }

    public function testBehaviorAddsMethod()
    {
        $connection = ConnectionManager::get('testing');
        $collection = new BaseCollection($connection);
        $collection->addBehavior('CakeMonga\Test\TestCollection\TestBehavior');
        $this->assertEquals('Hello World!', $collection->getHelloWorld());
    }

    public function testBehaviorGetsRemoved()
    {
        $connection = ConnectionManager::get('testing');
        $collection = new BaseCollection($connection);
        $collection->addBehavior('CakeMonga\Test\TestCollection\TestBehavior');
        $collection->removeBehavior('CakeMonga\Test\TestCollection\TestBehavior');
        $this->assertFalse($collection->hasBehavior('CakeMonga\Test\TestCollection\TestBehavior'));
    }

    public function testBehaviorEventsWork()
    {
        $connection = ConnectionManager::get('testing');
        $collection = new BaseCollection($connection, ['stop_event' => 'save']);
        $collection->addBehavior('CakeMonga\Test\TestCollection\TestBehavior');
        $results = $collection->save(['test' => true]);
        $one = $collection->findOne(['test' => true]);
        $this->assertEquals(1, $one['check']);
        $collection->truncate();
    }

    public function testInjectedCollectionIntoBehaviorRegistry()
    {
        $connection = ConnectionManager::get('testing');
        $collection_2 = new BaseCollection($connection);
        $registry = new MongoBehaviorRegistry($collection_2);
        $collection = new BaseCollection($connection, ['behaviors' => $registry]);
        $collection->addBehavior('CakeMonga\Test\TestCollection\TestBehavior');
        $results = $collection->save(['test' => true]);
        $one = $collection->findOne(['test' => true]);
        $this->assertEquals(1, $one['check']);
        $collection->truncate();
    }
}