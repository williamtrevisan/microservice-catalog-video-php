<?php

namespace Core\Domain\Entity;

use Core\Domain\Enum\Rating;
use Core\Domain\Exception\NotificationException;
use Core\Domain\Factory\VideoValidatorFactory;
use Core\Domain\ValueObject\{Image, Media, Uuid};
use DateTime;

class Video extends BaseEntity
{
    protected array $castMembersId = [];
    protected array $categoriesId = [];
    protected array $genresId = [];

    public function __construct(
        protected string $title,
        protected string $description,
        protected int $yearLaunched,
        protected int $duration,
        protected bool $opened,
        protected Rating $rating,
        protected ?Uuid $id = null,
        protected bool $published = false,
        protected ?Image $thumbFile = null,
        protected ?Image $thumbHalfFile = null,
        protected ?Image $bannerFile = null,
        protected ?Media $trailerFile = null,
        protected ?Media $videoFile = null,
        protected ?DateTime $createdAt = null,
    ) {
        parent::__construct();

        $this->id = $this->id ?? Uuid::random();
        $this->createdAt = $this->createdAt ?? new DateTime();

        $this->validate();
    }

    public function update(string $title, string $description): void
    {
        $this->title = $title;
        $this->description = $description;

        $this->validate();
    }

    public function addCastMember(string $castMemberId): void
    {
        $this->castMembersId[] = $castMemberId;
    }

    public function removeCastMember(string $castMemberId): void
    {
        $keyCastMemberId = array_search($castMemberId, $this->castMembersId);

        unset($this->castMembersId[$keyCastMemberId]);
    }

    public function addCategory(string $categoryId): void
    {
        $this->categoriesId[] = $categoryId;
    }

    public function removeCategory(string $categoryId): void
    {
        $keyCategoryId = array_search($categoryId, $this->categoriesId);

        unset($this->categoriesId[$keyCategoryId]);
    }

    public function addGenre(string $genreId): void
    {
        $this->genresId[] = $genreId;
    }

    public function removeGenre(string $genreId): void
    {
        $keyGenreId = array_search($genreId, $this->genresId);

        unset($this->genresId[$keyGenreId]);
    }

    public function thumbFile(): ?Image
    {
        return $this->thumbFile;
    }

    public function changeThumbFile(Image $thumbFile): void
    {
        $this->thumbFile = $thumbFile;
    }

    public function thumbHalfFile(): ?Image
    {
        return $this->thumbHalfFile;
    }

    public function changeThumbHalfFile(Image $thumbHalfFile): void
    {
        $this->thumbHalfFile = $thumbHalfFile;
    }

    public function bannerFile(): ?Image
    {
        return $this->bannerFile;
    }

    public function changeBannerFile(Image $bannerFile): void
    {
        $this->bannerFile = $bannerFile;
    }

    public function trailerFile(): ?Media
    {
        return $this->trailerFile;
    }

    public function changeTrailerFile(Media $trailerFile): void
    {
        $this->trailerFile = $trailerFile;
    }

    public function videoFile(): ?Media
    {
        return $this->videoFile;
    }

    public function changeVideoFile(Media $videoFile): void
    {
        $this->videoFile = $videoFile;
    }

    private function validate()
    {
        VideoValidatorFactory::create()->validate($this);

        if ($this->notification->hasErrors()) {
            throw new NotificationException($this->notification->messages('video'));
        }
    }
}
