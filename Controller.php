<?php

namespace app;

use yii;
use yii\console\Controller as BaseController;

class Controller extends BaseController
{
    public $component = 'daemon';

    public function actionStart()
    {
        Yii::$app->get($this->component)->start();
    }

    public function actionPure()
    {

    }
}