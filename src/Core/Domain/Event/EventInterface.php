<?php

namespace Core\Domain\Event;

interface EventInterface
{
    public function eventName(): string;
    public function payload(): array;
}
