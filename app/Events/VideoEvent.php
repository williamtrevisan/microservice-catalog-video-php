<?php

namespace App\Events;

use Core\Domain\Event\EventInterface;
use Core\UseCase\Interface\EventDispatcherInterface;

class VideoEvent implements EventDispatcherInterface
{
    public function dispatch(EventInterface $event): void
    {
        event($event);
    }
}
