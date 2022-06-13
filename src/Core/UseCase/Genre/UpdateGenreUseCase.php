<?php

namespace Core\UseCase\Genre;

class UpdateGenreUseCase
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
}
