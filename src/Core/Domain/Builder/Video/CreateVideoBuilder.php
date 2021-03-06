<?php

namespace Core\Domain\Builder\Video;

use Core\Domain\Entity\{BaseEntity, Video as VideoEntity};
use Core\Domain\ValueObject\{Image, Media};
use Core\Domain\Enum\{MediaStatus, Rating};

class CreateVideoBuilder implements VideoBuilderInterface
{
    protected ?BaseEntity $videoEntity;

    public function __construct()
    {
        $this->videoEntity = null;
    }

    public function createEntity(object $input): self
    {
        $this->videoEntity = new VideoEntity(
            title: $input->title,
            description: $input->description,
            yearLaunched: $input->yearLaunched,
            duration: $input->duration,
            opened: $input->opened,
            rating: Rating::from($input->rating),
        );

        $this->addAggregatesEntitiesId($input);

        return $this;
    }

    protected function addAggregatesEntitiesId(object $input)
    {
        foreach ($input->castMembersId as $castMemberId) {
            $this->videoEntity->addCastMember($castMemberId);
        }

        foreach ($input->categoriesId as $categoryId) {
            $this->videoEntity->addCategory($categoryId);
        }

        foreach ($input->genresId as $genreId) {
            $this->videoEntity->addGenre($genreId);
        }
    }

    public function getEntity(): BaseEntity
    {
        return $this->videoEntity;
    }

    public function addThumbFile(string $filePath): self
    {
        if (! $filePath) return $this;

        $this->videoEntity->changeThumbFile(thumbFile: new Image(filePath: $filePath));

        return $this;
    }

    public function addThumbHalfFile(string $filePath): self
    {
        if (! $filePath) return $this;

        $thumbHalfFile = new Image(filePath: $filePath);
        $this->videoEntity->changeThumbHalfFile(thumbHalfFile: $thumbHalfFile);

        return $this;
    }

    public function addBannerFile(string $filePath): self
    {
        if (! $filePath) return $this;

        $bannerFile = new Image(filePath: $filePath);
        $this->videoEntity->changeBannerFile(bannerFile: $bannerFile);

        return $this;
    }

    public function addTrailerFile(string $filePath): self
    {
        if (! $filePath) return $this;

        $trailerFile = new Media(filePath: $filePath, status: MediaStatus::Complete);
        $this->videoEntity->changeTrailerFile(trailerFile: $trailerFile);

        return $this;
    }

    public function addVideoFile(string $filePath, MediaStatus $status): self
    {
        if (! $filePath) return $this;

        $videoFile = new Media(filePath: $filePath, status: $status);
        $this->videoEntity->changeVideoFile(videoFile: $videoFile);

        return $this;
    }
}
