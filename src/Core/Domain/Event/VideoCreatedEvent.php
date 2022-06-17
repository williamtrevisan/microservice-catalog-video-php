<?php

namespace Core\Domain\Event;

use Core\Domain\Entity\BaseEntity;

class VideoCreatedEvent implements EventInterface
{
    public function __construct(protected readonly BaseEntity $video) {}

    public function eventName(): string
    {
        return 'video.created';
    }

    public function payload(): array
    {
        return [
            'resource_id' => $this->video->id(),
            'file_path' => $this->video->videoFile()->filePath,
        ];
    }
}
