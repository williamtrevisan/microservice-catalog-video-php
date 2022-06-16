<?php

namespace Core\UseCase\Interface;

use Core\Domain\Event\EventInterface;

interface EventDispatcherInterface
{
    public function dispatch(EventInterface $event): void;
}
