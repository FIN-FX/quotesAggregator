<?php

namespace app\components;

use yii\console\Request as BaseRequest;

class Request extends BaseRequest
{
    public function resolve()
    {
        $rawParams = $this->getParams();
        $route = 'controller/pure';
        if (isset($rawParams[0])) {
            $route = "controller/$rawParams[0]";
        }
        return [$route, []];
    }
}