<?php

namespace Core\UseCase\DTO\Video\Update;

class UpdateVideoInputDTO
{
    public function __construct(
        public string $id,
        public string $title,
        public string $description,
        public array $castMembersId = [],
        public array $categoriesId = [],
        public array $genresId = [],
        public array $thumbFile = [],
        public array $thumbHalfFile = [],
        public array $bannerFile = [],
        public array $trailerFile = [],
        public array $videoFile = [],
    ) {}
}
