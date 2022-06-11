<?php

namespace Core\UseCase\DTO\Category\delete;

class DeleteCategoryOutputDTO
{
    public function __construct(
        public bool $success
    ) {}
}