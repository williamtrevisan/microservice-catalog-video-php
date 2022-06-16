<?php

namespace Core\UseCase\DTO\Video\Create;

class CreateVideoOutputDTO
{
    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public int $year_launched,
        public int $duration,
        public bool $opened,
        public string $rating,
        public bool $published,
        public string $created_at,
        public array $castMembersId = [],
        public array $categoriesId = [],
        public array $genresId = [],
        public ?string $thumbFile = null,
        public ?string $thumbHalfFile = null,
        public ?string $bannerFile = null,
        public ?string $trailerFile = null,
        public ?string $videoFile = null,
    ) {}
}
