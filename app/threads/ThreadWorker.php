<?php

namespace app\threads;

class ThreadWorker extends \Worker
{
    public $id;
    public $dataProvider;

    public function __construct(DataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }


    /* include autoloader for Tasks */
    public function run()
    {
        $this->id = $this->getThreadId();
        require_once(__DIR__ . '/../../thread_service.php');
    }

    /* override default inheritance behaviour for the new threaded context */
    public function start($options = 0)
    {
        return parent::start(PTHREADS_INHERIT_NONE);
    }


}
