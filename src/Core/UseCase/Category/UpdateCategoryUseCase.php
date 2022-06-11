<?php

namespace Core\UseCase\Category;

use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\DTO\Category\update\UpdateCategoryInputDTO;
use Core\UseCase\DTO\Category\update\UpdateCategoryOutputDTO;

class UpdateCategoryUseCase
{
    public function __construct(
        protected readonly CategoryRepositoryInterface $categoryRepository
    ) {}

    public function execute(UpdateCategoryInputDTO $input): UpdateCategoryOutputDTO
    {
        $category = $this->categoryRepository->findById($input->id);

        $category->update(name: $input->name, description: $input->description);

        $category = $this->categoryRepository->update($category);

        return new UpdateCategoryOutputDTO(
            id: $category->id(),
            name: $category->name,
            description: $category->description,
            is_active: $category->isActive,
            created_at: $category->createdAt(),
        );
    }
}