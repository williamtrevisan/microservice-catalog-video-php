<?php

namespace Core\UseCase\DTO\Category\Update;

class UpdateCategoryInputDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string|null $description = null,
        public bool $isActive = true,
    ) {}
}
