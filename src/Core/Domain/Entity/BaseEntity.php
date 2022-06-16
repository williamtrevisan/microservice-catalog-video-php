<?php

namespace Core\Domain\Entity;

use Core\Domain\Notification\Notification;
use Exception;

abstract class BaseEntity
{
    protected readonly Notification $notification;

    public function __construct() {
        $this->notification = new Notification();
    }

    public function __get($property)
    {
        if (! isset($this->{$property})) {
            $className = get_class($this);

            throw new Exception("Property: $property not found in class $className");
        }

        return $this->{$property};
    }

    public function id(): string
    {
        return (string) $this->id;
    }

    public function createdAt(): string
    {
        return $this->createdAt->format('Y-m-d H:i:s');
    }
}
