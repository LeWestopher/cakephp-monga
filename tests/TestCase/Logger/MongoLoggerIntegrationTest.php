<?php

namespace CakeMonga\Test\TestCase\Logger;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;
use CakeMonga\Database\MongoConnection;
use CakeMonga\Logger\MongoLogger;
use CakeMonga\Test\TestLogger\BatchInsertTestLogger;
use CakeMonga\Test\TestLogger\DeleteTestLogger;
use CakeMonga\Test\TestLogger\InsertTestLogger;
use CakeMonga\Test\TestLogger\MockLogger;
use CakeMonga\Test\TestLogger\QueryTestLogger;
use CakeMonga\Test\TestLogger\ReplyTestLogger;
use CakeMonga\Test\TestLogger\TestLogger;
use CakeMonga\Test\TestException\DeletedException;
use CakeMonga\Test\TestLogger\UpdateTestLogger;


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

    /*public function testOnCmdInsert()
    {
        $connection = new MongoConnection();

        $mock = $this->getMockBuilder(MongoLogger::class)
            ->setMethods(['onCmdInsert'])
            ->getMock();

        $mock->expects($this->once())
            ->method('onCmdInsert')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->anything(),
                $this->anything()
            );

        $connection->logger($mock);
        $connection->logQueries(true);
        $coll = $connection->connect()->database('local')->collection('test');
        $coll->insert(['test' => 1]);
        $coll->truncate();
        unset($connection);
    }*/

    public function testOnInsert()
    {
        $connection = new MongoConnection();

        $mock = $this->getMockBuilder(MongoLogger::class)
            ->setMethods(['onInsert'])
            ->getMock();

        $mock->expects($this->once())
            ->method('onInsert')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->anything()
            );

        $connection->logger($mock);
        $connection->logQueries(true);
        $coll = $connection->connect()->database('local')->collection('test');
        $coll->insert(['test' => 1]);
        $coll->truncate();
        unset($connection);
    }

    public function testOnReply()
    {
        $connection = new MongoConnection();

        $mock = $this->getMockBuilder(MongoLogger::class)
            ->setMethods(['onReply'])
            ->getMock();

        $mock->expects($this->exactly(2))
            ->method('onReply')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->anything()
            );

        $connection->logger($mock);
        $connection->logQueries(true);
        $coll = $connection->connect()->database('local')->collection('test');
        $coll->insert(['test' => 1]);
        $coll->truncate();
        unset($connection);
    }

    /*public function testOnCmdUpdate()
    {
        $connection = new MongoConnection();

        $mock = $this->getMockBuilder(MongoLogger::class)
            ->setMethods(['onCmdUpdate'])
            ->getMock();

        $mock->expects($this->once())
            ->method('onCmdUpdate')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->anything(),
                $this->anything()
            );

        $connection->logger($mock);
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
    }*/

    public function testOnUpdate()
    {
        $connection = new MongoConnection();

        $mock = $this->getMockBuilder(MongoLogger::class)
            ->setMethods(['onUpdate'])
            ->getMock();

        $mock->expects($this->once())
            ->method('onUpdate')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->anything(),
                $this->anything()
            );

        $connection->logger($mock);
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

    /*public function testOnCmdDelete()
    {
        $connection = new MongoConnection();

        $mock = $this->getMockBuilder(MongoLogger::class)
            ->setMethods(['onCmdDelete'])
            ->getMock();

        $mock->expects($this->exactly(2))
            ->method('onCmdDelete')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->anything(),
                $this->anything()
            );

        $connection->logger($mock);
        $connection->logQueries(true);
        $coll = $connection->connect()->database('local')->collection('test');
        $coll->insert(['test' => 'abcd', 'count' => 1]);
        $coll->remove(['test' => 'abcd', 'count' => 1]);
        $coll->truncate();
        unset($connection);
    }*/

    public function testOnDelete()
    {
        $connection = new MongoConnection();

        $mock = $this->getMockBuilder(MongoLogger::class)
            ->setMethods(['onDelete'])
            ->getMock();

        $mock->expects($this->exactly(2))
            ->method('onDelete')
            /*->with(
                $this->anything(),
                $this->anything(),
                $this->anything(),
                $this->anything(),
                $this->anything()
            )*/;

        $connection->logger($mock);
        $connection->logQueries(true);
        $coll = $connection->connect()->database('local')->collection('test');
        $coll->insert(['test' => 'abcd', 'count' => 1]);
        $coll->remove(['test' => 'abcd', 'count' => 1]);
        $coll->truncate();
        unset($connection);
    }

    public function testOnBatchInsert()
    {
        $connection = new MongoConnection();

        $mock = $this->getMockBuilder(MongoLogger::class)
            ->setMethods(['onBatchInsert'])
            ->getMock();

        $mock->expects($this->once())
            ->method('onBatchInsert')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->anything(),
                $this->anything()
            );

        $connection->logger($mock);
        $connection->logQueries(true);
        $coll = $connection->connect()->database('local')->collection('test');
        $coll->insert([
            ['test' => 'abcd', 'count' => 1],
            ['test' => 'abcd', 'count' => 2]
        ]);
        $coll->truncate();
        unset($connection);
    }

    public function testOnQuery()
    {
        $connection = new MongoConnection();

        $mock = $this->getMockBuilder(MongoLogger::class)
            ->setMethods(['onQuery'])
            ->getMock();

        $mock->expects($this->once())
            ->method('onQuery')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->anything()
            );


        $connection->logger($mock);
        $connection->logQueries(true);
        $coll = $connection->connect()->database('local')->collection('test');
        $results = $coll->find()->toArray();
        unset($connection);
    }
}