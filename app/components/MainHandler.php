<?php

namespace app\components;

use yii;
use Swoole\Timer;
use app\threads\tasks\BNBSignalLoopTask;
use app\workers\BaseWorker;
use app\handlers\WebSocketHandler;

/**
 * Main server handler
 * @package app\components
 */
class MainHandler extends AbstractBaseHandler
{
    public $assets = ['BTC'];

    /**
     * @var \app\components\ThreadsHandler
     */
    public $threadsHandler;

    /**
     * @var \app\components\ConnectionsManager
     */
    public $connectionsManager;

    /** @var BaseWorker[] */
    public $workerInstances = [];

    protected function threadSignals()
    {
        foreach ($this->assets as $asset) {
            $task = new BNBSignalLoopTask($asset);
            $this->threadsHandler->addTask($task);
        }

        Timer::tick(10, function() {
            /** @var WebSocketHandler $server */
            $server = $this->getServer('ws');
            $keys = array_keys((array) $this->threadsHandler->dataProvider->data);
            foreach ($keys as $key) {
                $data = $this->threadsHandler->dataProvider->getLastAndRemove($key);
                if (!empty($data)) {
                    $server->sendMessageToClient(json_encode($data));
                }
            }
        });

        Timer::tick(1000, function() {
            var_dump($this->threadsHandler->dataProvider->counters);
        });
    }

    /**
     * Called when server starts
     */
    protected function onStart()
    {
        $this->connectionsManager = Yii::$app->get('connectionsManager');
        $this->threadsHandler = Yii::$app->get('threadsHandler');
        $this->threadSignals();
    }
}