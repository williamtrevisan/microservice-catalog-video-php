<?php

namespace Core\Domain\Entity;

use Core\Domain\Entity\Traits\MagicMethodsTrait;
use Core\Domain\Validation\DomainValidation;
use Core\Domain\ValueObject\Uuid;
use DateTime;

class Genre
{
    use MagicMethodsTrait;

    public function __construct(
        protected string $name,
        protected ?Uuid $id = null,
        protected array $categoriesId = [],
        protected bool $isActive = true,
        protected ?DateTime $createdAt = null,
    ) {
        $this->id = $this->id ?? Uuid::random();
        $this->createdAt = $this->createdAt ?? new DateTime();

        $this->validate();
    }

    public function activate()
    {
        $this->isActive = true;
    }

    public function deactivate()
    {
        $this->isActive = false;
    }

    public function update(string $name)
    {
        $this->name = $name;

        $this->validate();
    }

    public function addCategory(string $categoryId)
    {
        $this->categoriesId[] = $categoryId;
    }

    public function removeCategory(string $categoryId)
    {
        $keyCategoryId = array_search($categoryId, $this->categoriesId);

        unset($this->categoriesId[$keyCategoryId]);
    }

    private function validate()
    {
        DomainValidation::strMinLength($this->name);
        DomainValidation::strMaxLength($this->name);
    }
}
