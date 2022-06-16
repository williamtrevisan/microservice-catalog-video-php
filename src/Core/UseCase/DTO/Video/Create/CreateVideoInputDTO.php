<?php

namespace Core\UseCase\DTO\Video\Create;

class CreateVideoInputDTO
{
    public function __construct(
        public string $title,
        public string $description,
        public int $yearLaunched,
        public int $duration,
        public bool $opened,
        public string $rating,
        public array $castMembersId,
        public array $categoriesId,
        public array $genresId,
        public array $thumbFile = [],
        public array $thumbHalfFile = [],
        public array $bannerFile = [],
        public array $trailerFile = [],
        public array $videoFile = [],
    ) {}
}
