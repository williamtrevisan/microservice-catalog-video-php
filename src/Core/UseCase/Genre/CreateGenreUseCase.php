<?php

namespace Core\UseCase\Genre;

use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\Interface\TransactionInterface;
use CreateGenreInputDTO;
use CreateGenreOutputDTO;

class CreateGenreUseCase
{
    public function __construct(
        protected readonly CategoryRepositoryInterface $categoryRepository,
        protected readonly GenreRepositoryInterface $genreRepository,
        protected readonly TransactionInterface $transaction
    ) {}

    public function execute(CreateGenreInputDTO $input): CreateGenreOutputDTO
    {

    }
}
