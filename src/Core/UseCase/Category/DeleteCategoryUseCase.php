<?php

namespace Core\UseCase\Category;

use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\DTO\Category\CategoryInputDTO;
use Core\UseCase\DTO\Category\delete\DeleteCategoryOutputDTO;

class DeleteCategoryUseCase
{
    public function __construct(
        protected readonly CategoryRepositoryInterface $categoryRepository
    ) {}

    public function execute(CategoryInputDTO $input): DeleteCategoryOutputDTO
    {
        $hasBeenDeleted = $this->categoryRepository->delete($input->id);

        return new DeleteCategoryOutputDTO(
            success: $hasBeenDeleted
        );
    }
}