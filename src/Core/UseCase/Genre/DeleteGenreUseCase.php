<?php

namespace Core\UseCase\Genre;

use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\DTO\Genre\Delete\DeleteGenreOutputDTO;
use Core\UseCase\DTO\Genre\GenreInputDTO;

class DeleteGenreUseCase
{
    public function __construct(
        protected readonly GenreRepositoryInterface $genreRepository
    ) {}

    public function execute(GenreInputDTO $input): DeleteGenreOutputDTO
    {
        $hasBeenDeleted = $this->genreRepository->delete($input->id);

        return new DeleteGenreOutputDTO(
            success: $hasBeenDeleted
        );
    }
}
