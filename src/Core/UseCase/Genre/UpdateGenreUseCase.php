<?php

namespace Core\UseCase\Genre;

use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\DTO\Genre\Update\UpdateGenreInputDTO;
use Core\UseCase\DTO\Genre\Update\UpdateGenreOutputDTO;
use Core\UseCase\Interface\TransactionInterface;
use Exception;

class UpdateGenreUseCase
{
    public function __construct(
        protected readonly CategoryRepositoryInterface $categoryRepository,
        protected readonly GenreRepositoryInterface $genreRepository,
        protected readonly TransactionInterface $transaction
    ) {}

    public function execute(UpdateGenreInputDTO $input): UpdateGenreOutputDTO
    {
        $genre = $this->genreRepository->findById(id: $input->id);

        try {
            $this->validateCategoriesId($input->categoriesId);

            $genre->update(name: $input->name);
            if ($input->categoriesId) {
                foreach($input->categoriesId as $categoryId) $genre->addCategory($categoryId);
            }

            $genreDatabase = $this->genreRepository->update($genre);

            $this->transaction->commit();

            return new UpdateGenreOutputDTO(
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

        $arrayDifference = array_diff($categoriesId, $categories);
        if ($arrayDifference) {
            $message = sprintf(
                '%s with id: %s, not found in database',
                count($arrayDifference) > 1 ? 'Categories' : 'Category',
                implode(', ', $arrayDifference)
            );

            throw new NotFoundException($message);
        }
    }
}
