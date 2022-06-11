<?php

namespace Core\UseCase\Category;

use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\DTO\Category\CategoryInputDTO;
use Core\UseCase\DTO\Category\find\FindCategoryOutputDTO;

class FindCategoryUseCase
{
    public function __construct(
        protected readonly CategoryRepositoryInterface $categoryRepository
    ) {}

    public function execute(CategoryInputDTO $input): FindCategoryOutputDTO
    {
        $category = $this->categoryRepository->findById($input->id);

        return new FindCategoryOutputDTO(
            id: $category->id(),
            name: $category->name,
            description: $category->description,
            is_active: $category->isActive,
        );
    }
}