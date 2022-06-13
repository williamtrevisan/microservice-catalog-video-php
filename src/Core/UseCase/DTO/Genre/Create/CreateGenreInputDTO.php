<?php

class CreateGenreInputDTO
{
    public function __construct(
        public string $name,
        public array $categoriesId = [],
        public bool $isActive = true,
    ) {}
}
