<?php

namespace app\components;

use yii;
use app\threads\tasks\CommonTask;
use app\threads\ThreadsPool;
use yii\base\Component;
use app\threads\ThreadWorker;
use app\threads\DataProvider;

class ThreadsHandler extends Component
{
    public $apiPoolSize = 30;

    /** @var ThreadsPool */
    protected $apiPool;

    /** @var  DataProvider */
    public $dataProvider;

    public function init()
    {
        $this->dataProvider = new DataProvider();
        $this->apiPool = new ThreadsPool($this->apiPoolSize, ThreadWorker::class, [$this->dataProvider]);
    }

    public function addTask(CommonTask $task)
    {
        try {
            $this->apiPool->submit($task);
        } catch (\Exception $e) {
            Yii::error('API pool submit exception: ' . $e->getMessage());
            $this->apiPool = new ThreadsPool($this->apiPoolSize, ThreadWorker::class, [$this->dataProvider]);
        }
    }
}
