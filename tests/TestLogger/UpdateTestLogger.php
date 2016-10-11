<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/6/16
 * Time: 1:01 PM
 */

namespace CakeMonga\Test\TestLogger;


use CakeMonga\Logger\MongoLogger;
use CakeMonga\Test\TestException\UpdatedException;

class UpdateTestLogger extends MongoLogger
{
    public function onUpdate(array $server, array $write_options, array $update_options, array $protocol_options)
    {
        throw new UpdatedException('Updated');
    }
}