<?php

namespace app\handlers;

use Swoole\Process;
use Yii;
use yii\base\BaseObject;

abstract class BaseHandler extends BaseObject
{
    // Code was removed

    abstract protected function handleRequest();
}
