<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 10/6/16
 * Time: 1:00 PM
 */

namespace CakeMonga\Test\TestLogger;


use CakeMonga\Logger\MongoLogger;
use CakeMonga\Test\TestException\QueryException;

class QueryTestLogger extends MongoLogger
{
    public function onQuery(array $server, $arguments, array $query_options)
    {
        throw new QueryException('Query');
    }
}