<?php

namespace Core\Domain\Events;

class EventDispatcher
{
    private array $handlers;

    public function __construct()
    {
        $this->handlers = [];
    }

    public function register(string $eventName, $callback)
    {
        array_push(
            $this->handlers,
            [
                'eventName' => $eventName,
                'callback' => $callback
            ]
        );
    }

    public function dispatch(Event $event)
    {
        foreach ($this->handlers as $handler) {
            if ($handler['eventName'] === $event->getName()) {
                $handler['callback']($event);
            }
        }
    }
}
