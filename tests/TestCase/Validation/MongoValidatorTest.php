<?php

namespace CakeMonga\Test\TestCase\MongoCollection;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;
use CakeMonga\MongoCollection\BaseCollection;
use CakeMonga\Test\TestCollection\ValidationCollection;
use CakeMonga\Test\TestCollection\UpdateValidationCollection;
use CakeMonga\Validation\MongoValidator;

/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/4/16
 * Time: 3:09 AM
 */
class MongoValidatorTest extends TestCase
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

    public function testValidationFails()
    {
        $connection = ConnectionManager::get('testing');
        $collection = new ValidationCollection($connection);
        $results = $collection->save(['test' => null]);
        $this->assertTrue(isset($results['__errors']));
    }

    public function testValidationPassesWhenCreateRuleNotApplied()
    {
        $connection = ConnectionManager::get('testing');
        $collection = new UpdateValidationCollection($connection);
        $results = $collection->save(['test' => null]);
        $this->assertTrue($results);
        $this->assertTrue(!isset($results['__errors']));
    }

    public function testValidationFailsOnUpdate()
    {
        $connection = ConnectionManager::get('testing');
        $collection = new UpdateValidationCollection($connection);
        $collection->save(['check' => 'red']);
        $results = $collection->update(['test' => null], ['check' => 'red']);
        $this->assertTrue(isset($results['__errors']));
    }
}