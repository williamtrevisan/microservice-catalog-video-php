<?php

namespace Core\UseCase\Video;

use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\DTO\Video\Find\FindVideoOutputDTO;
use Core\UseCase\DTO\Video\VideoInputDTO;

class FindVideoUseCase
{
    public function __construct(
        protected readonly VideoRepositoryInterface $videoRepository
    ) {}

    public function execute(VideoInputDTO $input): FindVideoOutputDTO
    {
        $video = $this->videoRepository->findById(id: $input->id);

        return $this->output($video);
    }

    private function output(object $videoEntity): FindVideoOutputDTO
    {
        return new FindVideoOutputDTO(
            id: $videoEntity->id(),
            title: $videoEntity->title,
            description: $videoEntity->description,
            year_launched: $videoEntity->yearLaunched,
            duration: $videoEntity->duration,
            opened: $videoEntity->opened,
            rating: $videoEntity->rating->value,
            published: $videoEntity->published,
            created_at: $videoEntity->createdAt(),
            castMembersId: $videoEntity->castMembersId,
            categoriesId: $videoEntity->categoriesId,
            genresId: $videoEntity->genresId,
            thumbFile: $videoEntity->thumbFile()?->filePath(),
            thumbHalfFile: $videoEntity->thumbHalfFile()?->filePath(),
            bannerFile: $videoEntity->bannerFile()?->filePath(),
            trailerFile: $videoEntity->trailerFile()?->filePath,
            videoFile: $videoEntity->videoFile()?->filePath,
        );
    }
}
