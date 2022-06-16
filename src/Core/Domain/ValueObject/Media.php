<?php

namespace Core\Domain\ValueObject;

use Core\Domain\Enum\MediaStatus;

class Media
{
    public function __construct(
        protected string $filePath,
        protected MediaStatus $status,
        protected string $encodedFilePath = '',
    ) {}

    public function __get($property)
    {
        return $this->{$property};
    }
}
