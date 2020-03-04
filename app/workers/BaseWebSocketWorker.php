<?php

namespace app\workers;

/**
 * Class BaseWebSocketWorker
 * @package app\workers
 *
 * @property Server $server
 * @property Process $process
 */
abstract class BaseWebSocketWorker extends BaseWorker
{
    // Code was removed

    /**
     * @inheritDoc
     */
    abstract protected function onConnect($server, $fd, $from_id);

    /**
     * @inheritDoc
     */
    abstract protected function onOpen($server, $request);

    /**
     * @inheritDoc
     */
    abstract protected function onMessage($server, $frame);

    /**
     * @inheritDoc
     */
    abstract protected function onClose($server, $fd, $reactorId);
}
