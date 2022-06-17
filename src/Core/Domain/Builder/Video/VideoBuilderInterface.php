<?php

namespace Core\Domain\Builder\Video;

use Core\Domain\Builder\BuilderInterface;
use Core\Domain\Enum\MediaStatus;

interface VideoBuilderInterface extends BuilderInterface
{
    public function addThumbFile(string $filePath): self;
    public function addThumbHalfFile(string $filePath): self;
    public function addBannerFile(string $filePath): self;
    public function addTrailerFile(string $filePath): self;
    public function addVideoFile(string $filePath, MediaStatus $status): self;
}
