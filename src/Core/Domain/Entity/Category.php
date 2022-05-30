<?php

namespace Core\Domain\Entity;

use Core\Domain\Entity\Traits\MagicMethodsTrait;
use Core\Domain\Exception\EntityValidationException;

class Category
{
    use MagicMethodsTrait;

    public function __construct(
        protected string $id = '',
        protected string $name,
        protected string $description = '',
        protected bool $isActive = true
    ) {
        $this->validate();
    }

    public function activate()
    {
        $this->isActive = true;
    }

    public function disable()
    {
        $this->isActive = false;
    }

    public function update(string $name, string $description = '')
    {
        $this->name = $name;
        $this->description = $description ?? $this->description;

        $this->validate();
    }

    public function validate()
    {
        if (empty($this->name)) {
            throw new EntityValidationException("Invalid name");
        }

        if (
            strlen($this->name) > 255 ||
            strlen($this->name) < 3
        ) {
            throw new EntityValidationException("Invalid name");
        }

        if (
            $this->description !== '' &&
            (
                strlen($this->description) > 255 ||
                strlen($this->description) < 3
            )
        ) {
            throw new EntityValidationException("Invalid description");
        }
    }
}