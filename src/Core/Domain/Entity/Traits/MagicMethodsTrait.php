<?php

namespace Core\Domain\Entity\Traits;

use Exception;

trait MagicMethodsTrait
{
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
}