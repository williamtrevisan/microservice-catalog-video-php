<?php

namespace Core\UseCase\DTO\Category\Delete;

class DeleteCategoryOutputDTO
{
    public function __construct(
        public bool $success
    ) {}
}
