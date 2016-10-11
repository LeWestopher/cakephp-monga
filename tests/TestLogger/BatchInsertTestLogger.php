<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/6/16
 * Time: 1:03 PM
 */

namespace CakeMonga\Test\TestLogger;


use CakeMonga\Logger\MongoLogger;
use CakeMonga\Test\TestException\BatchInsertException;

class BatchInsertTestLogger extends MongoLogger
{
    public function onBatchInsert(array $server, array $write_options, array $batch, array $protocol_options)
    {
        throw new BatchInsertException('Batch insert');
    }
}