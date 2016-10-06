<?php

namespace CakeMonga\Logger;


/**
 * Class MongoLogger
 *
 * Extend this class to create logging callbacks for your CakeMonga instantiated MongoDB connections
 *
 * @package CakeMonga\Logger
 * @author Wes King
 * @license MIT
 */
class MongoLogger
{
    /**
     * Callback function for whenever the MongoDB instance is queried.  This feature is undocumented and php.net does
     * not provide a documentation page for this logging callback.
     *
     * @param array $server
     * @param $arguments
     * @param array $query_options
     * @return bool
     */
    public function onQuery(array $server, $arguments, array $query_options)
    {
        return true;
    }

    /**
     * Callback function for whenever the MongoDB instance receives an insert query.  See the php.net documentation
     * below to see information on the available arguments:
     *
     * http://php.net/manual/de/function.log-cmd-insert.php
     *
     * @param array $server
     * @param array $document
     * @param array $write_options
     * @param array $protocol_options
     * @return bool
     */
    public function onInsert(array $server, array $document, array $write_options, array $protocol_options)
    {
        return true;
    }
    /**
     * Callback function for whenever the MongoDB instance receives a delete query.  See the php.net documentation
     * below to see information on the available arguments:
     *
     * http://php.net/manual/de/function.log-cmd-delete.php
     *
     * @param array $server
     * @param array $write_options
     * @param array $delete_options
     * @param array $protocol_options
     * @return bool
     */
    public function onDelete(array $server, array $write_options, array $delete_options, array $protocol_options)
    {
        return true;
    }
    /**
     * Callback function for whenever the MongoDB instance receives an update query.  See the php.net documentation
     * below to see information on the available arguments:
     *
     * http://php.net/manual/de/function.log-cmd-update.php
     *
     * @param array $server
     * @param array $write_options
     * @param array $update_options
     * @param array $protocol_options
     * @return bool
     */
    public function onUpdate(array $server, array $write_options, array $update_options, array $protocol_options)
    {
        return true;
    }
    /**
     * Callback function for whenever the MongoDB instance receives an batch insert query.  See the php.net
     * documentation below to see information on the available arguments.  Note that the documentation page incorrectly
     * lists the callback function name as 'log_write_batch' when the callback function is actually 'log_batchinsert'.
     *
     * http://docs.php.net/manual/en/function.log-write-batch.php
     *
     * @param array $server
     * @param array $write_options
     * @param array $batch
     * @param array $protocol_options
     * @return bool
     */
    public function onBatchInsert(array $server, array $write_options, array $batch, array $protocol_options)
    {
        return true;
    }

    /**
     * Callback function for whenever the MongoDB instance replies.  See the php.net
     * documentation below to see information on the available arguments.
     *
     * http://php.net/manual/en/function.log-reply.php
     *
     * @param array $server
     * @param array $message_headers
     * @param array $operation_headers
     * @return bool
     */
    public function onReply(array $server, array $message_headers, array $operation_headers)
    {
        return true;
    }

    /**
     * Callback function for whenever a MongoCursor gets more results.  See the php.net
     * documentation below to see information on the available arguments.
     *
     * http://php.net/manual/en/function.log-getmore.php
     *
     * @param array $server
     * @param array $info
     * @return bool
     */
    public function onGetMore(array $server, array $info)
    {
        return true;
    }

    /**
     * Callback function for whenever a MongoCursor object is killed.  See the php.net
     * documentation below to see information on the available arguments.
     *
     * http://php.net/manual/en/function.log-killcursor.php
     *
     * @param array $server
     * @param array $info
     * @return bool
     */
    public function onKillCursor(array $server, array $info)
    {
        return true;
    }

    /**
     * Returns the context array for being converted into a stream context by the MongoConnection class.
     *
     * @return array
     */
    public function getContext()
    {
        $context = [
            'log_cmd_insert'    => [$this, 'onInsert'],
            'log_cmd_delete'    => [$this, 'onDelete'],
            'log_cmd_update'    => [$this, 'onUpdate'],
            'log_batchinsert'   => [$this, 'onBatchInsert'],
            'log_reply'         => [$this, 'onReply'],
            'log_getmore'       => [$this, 'onGetMore'],
            'log_killcursor'    => [$this, 'onKillCursor'],
            'log_query'         => [$this, 'onQuery']
        ];
        return $context;
    }
}