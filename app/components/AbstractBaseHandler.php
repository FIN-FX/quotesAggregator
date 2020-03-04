<?php

namespace app\components;

use yii;
use Swoole\Runtime;
use app\handlers\BaseHandler;

abstract class AbstractBaseHandler
{
    /**
     * @var array
     */
    public $workers = [];

    /**
     * @var BaseHandler[]
     */
    protected $servers = [];

    public $process;

    public function start()
    {
        try {
            Runtime::enableCoroutine();
            $this->onStart();
            foreach ($this->workers as $workerName => $workerConfig) {
                $this->servers[$workerName] = Yii::createObject($workerConfig);
            }
        } catch (\Exception $e) {
            \Yii::error($e->getMessage().__FILE__.' ('.__LINE__.')');
        }
    }

    /**
     * @return BaseHandler[]
     */
    public function getServers()
    {
        return $this->servers;
    }

    /**
     * @param string $name
     * @return BaseHandler|null
     */
    public function getServer($name)
    {
        return !empty($this->servers[$name]) ? $this->servers[$name] : null;
    }

    abstract protected function onStart();
}

