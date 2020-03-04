<?php

namespace app\handlers;

class WebSocketHandler extends BaseHandler
{
    protected function handleRequest()
    {
    }

    public function sendMessageToClient($message)
    {
        $this->writeToPipe($message);
    }
}
