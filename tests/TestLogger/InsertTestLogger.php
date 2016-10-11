<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/6/16
 * Time: 1:01 PM
 */

namespace CakeMonga\Test\TestLogger;


use CakeMonga\Logger\MongoLogger;
use CakeMonga\Test\TestException\InsertException;

class InsertTestLogger extends MongoLogger
{
    public function onInsert(array $server, array $document, array $write_options, array $protocol_options)
    {
        throw new InsertException('Insertttttt');
    }
}