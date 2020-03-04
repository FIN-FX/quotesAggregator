<?php

namespace app\threads\tasks;

/**
 * Common class for tasks
 * @package app\threads\tasks
 */
class CommonTask extends \Threaded
{
    public $workerClass = 'ThreadWorker';
}
