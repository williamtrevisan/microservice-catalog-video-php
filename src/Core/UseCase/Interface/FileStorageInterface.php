<?php

namespace Core\UseCase\Interface;

interface FileStorageInterface
{
    /**
     * @param string $filePath
     * @param array $_FILES[$file]
     * @return string
     */
    public function store(string $filePath, array $file): string;
    public function delete(string $filePath): bool;
}
