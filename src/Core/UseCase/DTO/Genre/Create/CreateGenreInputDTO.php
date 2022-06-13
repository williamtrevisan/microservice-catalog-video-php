<?php

namespace Core\UseCase\DTO\Genre\Create;

class CreateGenreInputDTO
{
    public function __construct(
        public string $name,
        public array $categoriesId = [],
        public bool $isActive = true,
    ) {}
}
