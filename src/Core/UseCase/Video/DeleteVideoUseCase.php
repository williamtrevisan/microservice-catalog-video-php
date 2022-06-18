<?php

namespace Core\UseCase\Video;

use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\DTO\Video\Delete\DeleteVideoOutputDTO;
use Core\UseCase\DTO\Video\VideoInputDTO;

class DeleteVideoUseCase
{
    public function __construct(
        protected readonly VideoRepositoryInterface $videoRepository
    ) {}

    public function execute(VideoInputDTO $input): DeleteVideoOutputDTO
    {
        $hasBeenDeleted = $this->videoRepository->delete($input->id);

        return new DeleteVideoOutputDTO(success: $hasBeenDeleted);
    }
}
