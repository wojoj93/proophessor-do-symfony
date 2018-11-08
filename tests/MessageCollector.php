<?php

namespace Tests;

use Prooph\Common\Event\ActionEvent;
use Prooph\ServiceBus\MessageBus;

class MessageCollector
{
    private $messages = [];

    public function __invoke(ActionEvent $actionEvent)
    {
        $message = $actionEvent->getParam(MessageBus::EVENT_PARAM_MESSAGE);
        $this->messages[] = $message;
    }

    public function all(): array
    {
        return $this->messages;
    }
}
