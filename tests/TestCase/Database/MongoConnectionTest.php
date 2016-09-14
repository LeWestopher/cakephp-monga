<?php
/**
 * CakePHP(tm) Tests <http://book.cakephp.org/view/1196/Testing>
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The Open Group Test Suite License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/view/1196/Testing CakePHP(tm) Tests
 * @since         2.2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace CakeMonga\Test\TestCase\Database;

use Cake\TestSuite\TestCase;
use CakeMonga\Database\MongoConnection;

class MongoConnectionTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testConfigName()
    {
        $mongo = new MongoConnection(['name' => 'mongo_db']);
        $this->assertEquals('mongo_db', $mongo->configName());
    }

    public function testGetDns()
    {
        $mongo = new MongoConnection(['dns' => 'mongodb://some.remote.address:27015']);
        $this->assertEquals('mongodb://some.remote.address:27015', $mongo->dns());
    }

    public function testSetDns()
    {
        $mongo = new MongoConnection();
        $mongo->dns('mongodb://some.remote.address:27015');
        $this->assertEquals('mongodb://some.remote.address:27015', $mongo->dns());
    }

    public function testGetMongoConfig()
    {
        $mongo = new MongoConnection([
            'ssl' => true,
            'password' => 'check',
            'gssapiServiceName' => '11111',
            'exclude_1' => 'a',
            'exclude_2' => 'b',
            'exclude_3' => 'c'
        ]);

        $expected = [
            'ssl' => true,
            'password' => 'check',
            'gssapiServiceName' => '11111'
        ];

        $this->assertEquals($expected, $mongo->getMongoConfig());

    }

    public function testRawConfig()
    {
        $mongo = new MongoConnection([
            'ssl' => true,
            'password' => 'check',
            'gssapiServiceName' => '11111',
            'exclude_1' => 'a',
            'exclude_2' => 'b',
            'exclude_3' => 'c'
        ]);

        $expected = [
            'ssl' => true,
            'password' => 'check',
            'gssapiServiceName' => '11111',
            'exclude_1' => 'a',
            'exclude_2' => 'b',
            'exclude_3' => 'c'
        ];

        $this->assertEquals($expected, $mongo->config());
    }

    public function testConnect()
    {
        $mongo = new MongoConnection();
        $connection = $mongo->connect();

        $this->assertEquals('League\Monga\Connection', get_class($connection));
    }

    public function testConnected()
    {
        $mongo = new MongoConnection();
        $mongo->connect();

        $this->assertEquals(true, $mongo->connected());
    }

    public function testNotConnected()
    {
        $mongo = new MongoConnection();

        $this->assertEquals(false, $mongo->connected());
    }

    public function testNoDefaultDatabase()
    {
        $mongo = new MongoConnection(['name' => 'mongo_db']);
        $expected = sprintf('You have not configured a default database for Datasource %s yet.', 'mongo_db');
        try {
            $mongo->getDefaultDatabase();
        } catch (\Exception $e) {
            $result = $e->getMessage();
        }
        $this->assertEquals($expected, $result);
    }

    public function testTransactionalNoOp()
    {
        $mongo = new MongoConnection();
        $result = $mongo->transactional(function () {});
        $this->assertEquals(true, $result);
    }

    public function testDisableConstraintsNoOp()
    {
        $mongo = new MongoConnection();
        $result = $mongo->disableConstraints(function(){});
        $this->assertEquals(true, $result);
    }

    public function testEnableQueryLogging()
    {
        $mongo = new MongoConnection();
        $mongo->logQueries(true);
        $result = $mongo->logQueries();
        $this->assertEquals(true, $result);
    }

    public function testDisableQueryLogging()
    {
        $mongo = new MongoConnection();
        $mongo->logQueries(false);
        $result = $mongo->logQueries();
        $this->assertEquals(false, $result);
    }

    public function testEmptyConfigNameString()
    {
        $mongo = new MongoConnection();
        $this->assertEquals('', $mongo->configName());
    }

    public function testLoggerNoOp()
    {
        $mongo = new MongoConnection();
        $this->assertTrue($mongo->logger());
    }
}