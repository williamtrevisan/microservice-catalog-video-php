<?php

namespace Core\UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\DTO\Genre\Create\CreateGenreInputDTO;
use Core\UseCase\DTO\Genre\Create\CreateGenreOutputDTO;
use Core\UseCase\Interface\TransactionInterface;
use Exception;

class CreateGenreUseCase
{
    public function __construct(
        protected readonly CategoryRepositoryInterface $categoryRepository,
        protected readonly GenreRepositoryInterface $genreRepository,
        protected readonly TransactionInterface $transaction
    ) {}

    public function execute(CreateGenreInputDTO $input): CreateGenreOutputDTO
    {
        try {
            $this->validateCategoriesId($input->categoriesId);

            $genreEntity = new Genre(
                name: $input->name,
                categoriesId: $input->categoriesId,
                isActive: $input->isActive,
            );

            $genreDatabase = $this->genreRepository->insert($genreEntity);

            $this->transaction->commit();

            return new CreateGenreOutputDTO(
                id: $genreDatabase->id(),
                name: $genreDatabase->name,
                is_active: $genreDatabase->isActive,
                created_at: $genreDatabase->createdAt(),
            );
        } catch (Exception $exception) {
            $this->transaction->rollback();

            throw $exception;
        }
    }

    /**
     * @throws NotFoundException
     */
    private function validateCategoriesId(array $categoriesId = [])
    {
        $categories = $this->categoryRepository->getIdsByListId($categoriesId);

        if (count($categoriesId) !== count($categories)) {
            throw new NotFoundException('Categories not found in database');
        }
    }
}
