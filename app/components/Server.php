<?php

namespace app\components;

use yii;
use yii\base\Component;

class Server extends Component
{
    protected static $worker;

    public $handler;

    public function start()
    {
        /** @var \app\components\MainHandler $worker */
        $worker = Yii::createObject($this->handler);
        self::$worker = $worker;
        $worker->start();
    }

    /**
     * @return \app\components\MainHandler
     */
    public static function getWorker()
    {
        return self::$worker;
    }
}