<?php

namespace Core\UseCase\DTO\Video\Delete;

class DeleteVideoOutputDTO
{
    public function __construct(public bool $success) {}
}
