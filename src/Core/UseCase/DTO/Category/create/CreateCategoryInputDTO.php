<?php

namespace Core\UseCase\DTO\Category\create;

class CreateCategoryInputDTO
{
    public function __construct(
        public string $name,
        public string $description = '',
        public bool $isActive = true,
    ) {}
}