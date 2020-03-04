<?php

namespace app\models;

use app\components\Server;

/**
 * Model of connected client
 * @package app\models
 */
class Client
{
    public $userId;

    /**
     * Validation of client token and fill object
     * @param $connectionId
     * @return bool
     */
    public function validate($connectionId = null)
    {
        $this->userId = microtime(true);
        if ($connectionId) {
            Server::getWorker()->connectionsManager->addConnection($this, $connectionId);
        }
        return true;
    }
}
