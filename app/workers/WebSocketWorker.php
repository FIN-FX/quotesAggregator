<?php

namespace app\workers;

use yii;
use app\models\Client;
use Swoole\Server;

class WebSocketWorker extends BaseWebSocketWorker
{
    /**
     * @inheritDoc
     */
    protected function onWorkerStart($server, $worker_id)
    {
        \app\components\Server::getWorker()->workerInstances[$worker_id] = $this;
        parent::onWorkerStart($server, $worker_id);
    }

    /**
     * @inheritDoc
     */
    protected function onConnect($server, $fd, $from_id)
    {
    }

    /**
     * @inheritDoc
     */
    protected function onOpen($server, $request)
    {
        /** @var \Swoole\WebSocket\Server $server */
        /** @var \Swoole\Http\Request $request */
        try {
            // Init client model
            $client = new Client();
            $client->validate($request->fd);
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            $server->disconnect($request->fd, 4001, 'Unauthorized');
        }
    }

    /**
     * @inheritDoc
     */
    protected function onMessage($server, $frame)
    {
    }

    /**
     * @inheritDoc
     */
    protected function onClose($server, $fd, $reactorId)
    {
        \app\components\Server::getWorker()->connectionsManager->removeConnection($fd);
    }

    /**
     * Method to handle messages from a main process. Need to write a message to the pipe to send this message to client (call the method $this->writeToPipe(string $data))
     */
    protected function handleResponse()
    {
        $data = $this->readFromPipe();
        foreach ($data as $row) {
            if (!empty($row)) {
                \app\components\Server::getWorker()->connectionsManager->sendToAllConnections($row);
            }
        }
    }

    /**
     * This event workerStop happens when the worker process stops.
     *
     * @param Server $server - the swoole server object
     * @param int $worker_id
     */
    protected function onWorkerStop($server, $worker_id)
    {
        if (isset(\app\components\Server::getWorker()->workerInstances[$worker_id])) {
            unset(\app\components\Server::getWorker()->workerInstances[$worker_id]);
        }
    }

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
    protected function onWorkerError($server, $worker_id, $worker_pid, $exit_code, $signal)
    {
        Yii::error('WebSocket worker error: Code: ' . $exit_code . ' Signal: ' . $signal);
    }

    public function sendMessageToClient($fd, $data)
    {
        $obj = new \stdClass();
        $obj->fd = $fd;
        $obj->data = json_encode($data);
        $this->sendToRecipient($obj);
    }

    public function closeConnectionById($fd, $data)
    {
        $obj = new \stdClass();
        $obj->fd = $fd;
        $obj->data = json_encode($data);
        $this->disconnect($obj);
    }
}
