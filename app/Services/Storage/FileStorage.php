<?php

namespace App\Services\Storage;

use Core\UseCase\Interface\FileStorageInterface;

class FileStorage implements FileStorageInterface
{
    public function store(string $filePath, array $file): string
    {
        // TODO: Implement store() method.
    }

    public function delete(string $filePath): bool
    {
        // TODO: Implement delete() method.
    }
}
