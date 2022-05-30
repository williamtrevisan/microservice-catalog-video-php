<?php

namespace Core\Domain\Entity;

use Core\Domain\Entity\Traits\MagicMethodsTrait;

class Category
{
    use MagicMethodsTrait;

    public function __construct(
        protected string $id = '',
        protected string $name,
        protected string $description = '',
        protected bool $isActive = true
    ) {}

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
    }
}