<?php

namespace app\workers;

use yii;
use Swoole;
use yii\base\BaseObject;

class ServerFactory extends BaseObject
{
    const TYPE_HTTP = 'http';
    const TYPE_SSL = 'ssl';
    const TYPE_TCP = 'tcp';
    const TYPE_UDP = 'udp';
    const TYPE_WS = 'ws';

    /** @var string */
    public $type;
    /** @var string */
    public $host = '0.0.0.0';
    /** @var int */
    public $port;
    /** @var int */
    public $mode = SWOOLE_BASE;
    /** @var int */
    public $sock_type;
    /** @var array */
    public $setting = [];

    /**
     * @return Swoole\Server
     * @throws yii\base\InvalidConfigException
     */
    public function makeServer()
    {
        if (empty($this->type)) {
            throw new yii\base\InvalidConfigException("Undefined server type");
        }
        if (empty($this->port)) {
            throw new yii\base\InvalidConfigException("Undefined port for {$this->type} server");
        }
        switch (strtolower($this->type)) {
            case self::TYPE_HTTP:
                $sock_type = $this->sock_type ?? SWOOLE_SOCK_TCP;
                $server = new Swoole\Http\Server($this->host, $this->port, $this->mode, $sock_type);
                break;
            case self::TYPE_SSL:
                $sock_type = $this->sock_type ?? SWOOLE_SOCK_TCP | SWOOLE_SSL;
                $server = new Swoole\Http\Server($this->host, $this->port, $this->mode, $sock_type);
                break;
            case self::TYPE_TCP:
                $sock_type = $this->sock_type ?? SWOOLE_SOCK_TCP;
                $server = new Swoole\Server($this->host, $this->port, $this->mode, $sock_type);
                break;
            case self::TYPE_UDP:
                $sock_type = $this->sock_type ?? SWOOLE_SOCK_UDP;
                $server = new Swoole\Server($this->host, $this->port, $this->mode, $sock_type);
                break;
            case self::TYPE_WS:
                $sock_type = $this->sock_type ?? SWOOLE_SOCK_TCP;
                $server = new Swoole\WebSocket\Server($this->host, $this->port, $this->mode, $sock_type);
                break;
            default:
                throw new yii\base\InvalidConfigException("Unknown server type \"{$this->type}\"");
        }
        if (!empty($this->setting)) {
            $server->set($this->setting);
        }

        return $server;
    }
}
