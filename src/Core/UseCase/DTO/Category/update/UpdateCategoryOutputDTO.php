<?php

namespace Core\UseCase\DTO\Category\update;

class UpdateCategoryOutputDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description = '',
        public bool $is_active = true,
        public string $created_at = '',
    ) {}
}