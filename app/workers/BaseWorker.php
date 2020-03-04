<?php

namespace app\workers;

use Swoole\Process;
use Swoole\Server;
use yii;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

abstract class BaseWorker extends BaseObject
{
    // Code was removed

    /**
     * Send data to the client.
     *
     * @param object $data - contains int $fd, string $data
     */
    abstract protected function sendToRecipient($data);

    /**
     * Method to handle messages from a main process. Need to write a message to the pipe to send this message to client (call the method $this->writeToPipe(string $data))
     */
    abstract protected function handleResponse();

    /**
     * The event workerStart happens when the worker process or task worker process starts.
     *
     * @param Server $server - the swoole server object
     * @param int $worker_id - According to the value of $worker_id, if $worker_id >= $server->setting['worker_num'], the worker is a task worker process otherwise it is a worker process. There is no relation between worker_id and pid of process.
     */
    abstract protected function onWorkerStart($server, $worker_id);

    /**
     * This event workerStop happens when the worker process stops.
     *
     * @param Server $server - the swoole server object
     * @param int $worker_id
     */
    abstract protected function onWorkerStop($server, $worker_id);

    /**
     * When there is error or exception in the worker process or task worker process, the event workerError happens in the manager process.
     *
     * @param Server $server - the swoole server object
     * @param int $worker_id
     * @param int $worker_pid
     * @param int $exit_code
     * @param int $signal
     * @return void
     */
    abstract protected function onWorkerError($server, $worker_id, $worker_pid, $exit_code, $signal);
}
