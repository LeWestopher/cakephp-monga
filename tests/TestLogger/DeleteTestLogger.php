<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/6/16
 * Time: 1:02 PM
 */

namespace CakeMonga\Test\TestLogger;


use CakeMonga\Logger\MongoLogger;
use CakeMonga\Test\TestException\DeletedException;

class DeleteTestLogger extends MongoLogger
{
    public function onDelete(array $server, array $write_options, array $delete_options, array $protocol_options)
    {
        throw new DeletedException('Deleted');
    }
}