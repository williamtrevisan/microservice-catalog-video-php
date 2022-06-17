<?php

namespace Core\Domain\Builder\Video;

use Core\Domain\Entity\Video as VideoEntity;
use Core\Domain\Enum\Rating;
use Core\Domain\ValueObject\Uuid;
use DateTime;

class UpdateVideoBuilder extends CreateVideoBuilder
{
    public function createEntity(object $input): self
    {
        $this->videoEntity = new VideoEntity(
            id: new Uuid($input->id),
            title: $input->title,
            description: $input->description,
            yearLaunched: $input->yearLaunched,
            duration: $input->duration,
            opened: $input->opened,
            rating: Rating::from($input->rating),
            createdAt: new DateTime($input->createdAt),
        );

        $this->addAggregatesEntitiesId($input);

        return $this;
    }
}
