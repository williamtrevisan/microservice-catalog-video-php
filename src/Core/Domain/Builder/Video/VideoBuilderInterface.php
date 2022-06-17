<?php

namespace Core\Domain\Builder\Video;

use Core\Domain\Builder\BuilderInterface;
use Core\Domain\Enum\MediaStatus;

interface VideoBuilderInterface extends BuilderInterface
{
    public function addThumbFile(string $filePath): void;
    public function addThumbHalfFile(string $filePath): void;
    public function addBannerFile(string $filePath): void;
    public function addTrailerFile(string $filePath): void;
    public function addVideoFile(string $filePath, MediaStatus $status): void;
}
