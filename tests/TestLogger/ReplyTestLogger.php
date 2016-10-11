<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/6/16
 * Time: 1:04 PM
 */

namespace CakeMonga\Test\TestLogger;


use CakeMonga\Logger\MongoLogger;
use CakeMonga\Test\TestException\ReplyException;

class ReplyTestLogger extends MongoLogger
{
    public function onReply(array $server, array $message_headers, array $operation_headers)
    {
        throw new ReplyException('Reply');
    }
}