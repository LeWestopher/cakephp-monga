<?php

namespace CakeMonga\Test\TestCase\Logger;
use Cake\TestSuite\TestCase;
use CakeMonga\Database\MongoConnection;


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

    public function testOnInsert()
    {
        $connection = new MongoConnection();

        $mock = $this->getMockBuilder('CakeMonga\Logger\MongoLogger')
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

    public function testOnUpdate()
    {
        $connection = new MongoConnection();

        $mock = $this->getMockBuilder('CakeMonga\Logger\MongoLogger')
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

    public function testOnDelete()
    {
        $connection = new MongoConnection();

        $mock = $this->getMockBuilder('CakeMonga\Logger\MongoLogger')
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

        $mock = $this->getMockBuilder('CakeMonga\Logger\MongoLogger')
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

        $mock = $this->getMockBuilder('CakeMonga\Logger\MongoLogger')
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