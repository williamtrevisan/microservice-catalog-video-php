<?php

namespace Core\UseCase\DTO\Genre\Delete;

class DeleteGenreOutputDTO
{
    public function __construct(public bool $success) {}
}
