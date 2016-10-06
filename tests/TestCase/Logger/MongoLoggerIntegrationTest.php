<?php

namespace CakeMonga\Test\TestCase\Logger;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;
use CakeMonga\Database\MongoConnection;
use CakeMonga\Test\TestLogger\BatchInsertTestLogger;
use CakeMonga\Test\TestLogger\DeleteTestLogger;
use CakeMonga\Test\TestLogger\InsertTestLogger;
use CakeMonga\Test\TestLogger\MockLogger;
use CakeMonga\Test\TestLogger\QueryTestLogger;
use CakeMonga\Test\TestLogger\ReplyTestLogger;
use CakeMonga\Test\TestLogger\TestLogger;
use CakeMonga\Test\TestException\DeletedException;
use CakeMonga\Test\TestLogger\UpdateTestLogger;

/**
 * MongoLogger Tests
 *
 * A special note about the way these tests are written - the logger object passed to the stream context seems to have
 * issues maintaining state when passed into the Connection object, so to ensure that each of the logging callbacks
 * were properly being accessed, I created loggers for each callback to avoid having multiple callbacks being triggered
 * by one test.  I also created specific exceptions to be thrown within those callback loggers to ensure that the
 * primary functionality of the test, the callback being triggered, was working as intended.  This is why you see the
 * tests below testing for an expected exception, because the specific logger callbacks being tested for throw those
 * exceptions, meaning that the test has passed.
 */
class MongoLoggerIntegrationTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }
    /**
     * @expectedException \CakeMonga\Test\TestException\InsertException
     */
    public function testOnInsert()
    {
        $connection = new MongoConnection();
        $logger = new InsertTestLogger();
        $connection->logger($logger);
        $connection->logQueries(true);
        $coll = $connection->connect()->database('local')->collection('test');
        $coll->insert(['test' => 1]);
        $coll->truncate();
        unset($connection);
    }
    /**
     * @expectedException \CakeMonga\Test\TestException\ReplyException
     */
    public function testOnReply()
    {
        $connection = new MongoConnection();
        $logger = new ReplyTestLogger();
        $connection->logger($logger);
        $connection->logQueries(true);
        $coll = $connection->connect()->database('local')->collection('test');
        $coll->insert(['test' => 1]);
        $coll->truncate();
        unset($connection);
    }
    /**
     * @expectedException \CakeMonga\Test\TestException\UpdatedException
     */
    public function testOnUpdate()
    {
        $connection = new MongoConnection();
        $logger = new UpdateTestLogger();
        $connection->logger($logger);
        $connection->logQueries(true);
        $coll = $connection->connect()->database('local')->collection('test');
        $coll->insert(['test' => 'abcd', 'count' => 1]);
        $obj = $coll->findOne(function ($query) {
            $query->where('test', 'abcd');
        });
        $obj['count'] = 2;
        $coll->save($obj);
        $coll->truncate();
        unset($connection);
    }
    /**
     * @expectedException \CakeMonga\Test\TestException\DeletedException
     */
    public function testOnDelete()
    {
        $connection = new MongoConnection();
        $logger = new DeleteTestLogger();
        $connection->logger($logger);
        $connection->logQueries(true);
        $coll = $connection->connect()->database('local')->collection('test');
        $coll->insert(['test' => 'abcd', 'count' => 1]);
        $coll->remove(['test' => 'abcd', 'count' => 1]);
        $coll->truncate();
        unset($connection);
    }
    /**
     * @expectedException \CakeMonga\Test\TestException\BatchInsertException
     */
    public function testOnBatchInsert()
    {
        $connection = new MongoConnection();
        $logger = new BatchInsertTestLogger();
        $connection->logger($logger);
        $connection->logQueries(true);
        $coll = $connection->connect()->database('local')->collection('test');
        $coll->insert([
            ['test' => 'abcd', 'count' => 1],
            ['test' => 'abcd', 'count' => 2]
        ]);
        $coll->truncate();
        unset($connection);
    }
    /**
     * @expectedException \CakeMonga\Test\TestException\QueryException
     */
    public function testOnQuery()
    {
        $connection = new MongoConnection();
        $logger = new QueryTestLogger();
        $connection->logger($logger);
        $connection->logQueries(true);
        $coll = $connection->connect()->database('local')->collection('test');
        $results = $coll->find()->toArray();
        unset($connection);
    }
}