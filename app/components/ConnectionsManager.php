<?php

namespace app\components;

use app\models\Client;
use app\workers\WebSocketWorker;

/**
 * Class used to manage project connections
 * @package app\components
 */
class ConnectionsManager
{
    /**
     * List of connections by connection id
     * @var array
     */
    public $connections = [];

    /**
     * List of connections by client id
     * @var array
     */
    public $connectionsByUserId = [];

    /**
     * List of client objects by client id
     * @var array
     */
    public $clients = [];

    /**
     * Adding new client connection
     * @param \app\models\Client $client
     * @param $connectionId
     * @return bool
     */
    public function addConnection(Client $client, $connectionId)
    {
        $this->connections[$connectionId] = $client->userId;
        if (empty($this->clients[$client->userId])) {
            $this->clients[$client->userId] = $client;
            $this->connectionsByUserId[$client->userId] = [$connectionId => $connectionId];
        } else {
            $this->connectionsByUserId[$client->userId][$connectionId] = $connectionId;
        }
        return true;
    }

    /**
     * For service connection clients
     * @param \app\models\Client $client
     */
    public function addClient(Client $client)
    {
        if (empty($this->clients[$client->userId])) {
            $this->clients[$client->userId] = $client;
        }
    }

    /**
     * Remove existing client connection
     * @param $connectionId
     * @return bool
     */
    public function removeConnection($connectionId)
    {
        if (!empty($this->connections[$connectionId]) &&
            !empty($this->clients[$this->connections[$connectionId]]) &&
            !empty($this->connectionsByUserId[$this->connections[$connectionId]])
        ) {
            unset($this->connectionsByUserId[$this->connections[$connectionId]][$connectionId]);
            if (empty($this->connectionsByUserId[$this->connections[$connectionId]])) {
                unset($this->connectionsByUserId[$this->connections[$connectionId]]);
                unset($this->clients[$this->connections[$connectionId]]);
            }
            unset($this->connections[$connectionId]);
            return true;
        }
        return false;
    }

    /**
     * Get connections for current user
     * @param $userId
     * @return Client
     */
    public function getUserObject($userId)
    {
        $result = [];
        if (!empty($this->clients[$userId])) {
            $result = $this->clients[$userId];
        }
        return $result;
    }

    /**
     * Get connections for current user
     * @param $connectionId
     * @return array
     */
    public function getObjectByConnectionId($connectionId)
    {
        $result = [];
        if (!empty($this->connections[$connectionId]) && !empty($this->clients[$this->connections[$connectionId]])) {
            $result = $this->clients[$this->connections[$connectionId]];
        }
        return $result;
    }

    public function sendToConnections($connections, string $commandData)
    {
        foreach ($connections as $connectionId) {
            /** @var WebSocketWorker[] $instances */
            $instances = Server::getWorker()->workerInstances;
            foreach ($instances as $instance) {
                $instance->sendMessageToClient($connectionId, $commandData);
            }
        }
    }

    public function sendToAllConnections(string $commandData)
    {
        $this->sendToConnections(array_keys($this->connections), $commandData);
    }
}
