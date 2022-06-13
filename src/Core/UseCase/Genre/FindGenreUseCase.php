<?php

namespace Core\UseCase\Genre;

use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\DTO\Genre\Find\FindGenreOutputDTO;
use Core\UseCase\DTO\Genre\GenreInputDTO;

class FindGenreUseCase
{
    public function __construct(
        protected readonly GenreRepositoryInterface $genreRepository
    ) {}

    public function execute(GenreInputDTO $input): FindGenreOutputDTO
    {
        $genre = $this->genreRepository->findById(id: $input->id);

        return new FindGenreOutputDTO(
            id: $genre->id(),
            name: $genre->name,
            is_active: $genre->isActive,
            created_at: $genre->createdAt(),
        );
    }
}
