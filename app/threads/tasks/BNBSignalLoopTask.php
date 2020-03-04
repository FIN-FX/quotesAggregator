<?php

namespace app\threads\tasks;

use yii;
use React\EventLoop\Factory;
use React\Socket\Connector;
use Ratchet\Client\Connector as RConnector;
use Ratchet\Client\WebSocket;
use Ratchet\RFC6455\Messaging\MessageInterface;
use app\threads\ThreadWorker;

class BNBSignalLoopTask extends CommonTask
{
    public $workerClass = 'ThreadWorker';

    public $asset;

    public function __construct($asset)
    {
        $this->asset = $asset;
    }

    /**
     * Init connection to server with thread identifier
     * @param $connector
     * @param $url
     * @param $loop
     * @param $app
     * @param $ident
     */
    protected function connectToServer($connector, $url, $loop, $app, $ident)
    {
        $connector($url)
            ->then($app, function (\Exception $e) use ($loop, $ident) {
                Yii::error(" Could not connect: {$e->getMessage()}".__FILE__.' ('.__LINE__.')');
                /** @var Factory $loop */
                $loop->stop();
            });
    }

    /**
     * Start main process in separate thread
     */
    public function run()
    {
        /** @var ThreadWorker $worker */
        $worker = $this->worker;
        $prefix = $this->asset.'_thread';

        $loop = Factory::create();
        $reactConnector = new Connector($loop, [
            'dns' => '8.8.8.8',
            'timeout' => 10
        ]);
        $connector = new RConnector($loop, $reactConnector);

        $url = 'wss://stream.binance.com:9443/stream?streams='.strtolower($this->asset).'usdt@bookTicker';
        $app = function (WebSocket $conn) use ($connector, $loop, $prefix, $worker, &$app, $url) {
            $conn->on('message', function (MessageInterface $msg) use ($conn, $prefix, $worker) {
                $msgp = json_decode($msg, true);
                if (!empty($msgp['data'])) {
                    $worker->dataProvider->set($prefix, $msgp['data']);
                    $worker->dataProvider->inc($prefix);
                }
            });

            $conn->on('close', function ($code = null, $reason = null) use ($connector, $loop, $app, $url, $prefix) {
                Yii::error("Connection closed ({$code} - {$reason})");
                // Reconnect after 3 seconds
                $loop->addTimer(3, function () use ($connector, $loop, $app, $url, $prefix) {
                    $this->connectToServer($connector, $url, $loop, $app, $prefix);
                });
            });
        };
        $this->connectToServer($connector, $url, $loop, $app, $prefix);

        $loop->run();
    }
}