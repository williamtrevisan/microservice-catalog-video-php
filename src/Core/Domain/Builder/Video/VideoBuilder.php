<?php

namespace Core\Domain\Builder\Video;

use Core\Domain\Entity\{BaseEntity, Video as VideoEntity};
use Core\Domain\ValueObject\{Image, Media};
use Core\Domain\Enum\{MediaStatus, Rating};

class VideoBuilder implements VideoBuilderInterface
{
    private ?BaseEntity $videoEntity;

    public function __construct()
    {
        $this->videoEntity = null;
    }

    public function createEntity(object $input): void
    {
        $this->videoEntity = new VideoEntity(
            title: $input->title,
            description: $input->description,
            yearLaunched: $input->yearLaunched,
            duration: $input->duration,
            opened: $input->opened,
            rating: Rating::from($input->rating),
        );

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

    public function addThumbFile(string $filePath): void
    {
        $this->videoEntity->changeThumbFile(thumbFile: new Image(filePath: $filePath));
    }

    public function addThumbHalfFile(string $filePath): void
    {
        $thumbHalfFile = new Image(filePath: $filePath);

        $this->videoEntity->changeThumbHalfFile(thumbHalfFile: $thumbHalfFile);
    }

    public function addBannerFile(string $filePath): void
    {
        $bannerFile = new Image(filePath: $filePath);

        $this->videoEntity->changeBannerFile(bannerFile: $bannerFile);
    }

    public function addTrailerFile(string $filePath): void
    {
        $trailerFile = new Media(filePath: $filePath, status: MediaStatus::Complete);

        $this->videoEntity->changeTrailerFile(trailerFile: $trailerFile);
    }

    public function addVideoFile(string $filePath, MediaStatus $status): void
    {
        $videoFile = new Media(filePath: $filePath, status: $status);

        $this->videoEntity->changeVideoFile(videoFile: $videoFile);
    }
}
