<?php

namespace Core\UseCase\Category;

use Core\Domain\Entity\Category;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\DTO\Category\create\CreateCategoryInputDTO;
use Core\UseCase\DTO\Category\create\CreateCategoryOutputDTO;

class CreateCategoryUseCase
{
    public function __construct(
        protected readonly CategoryRepositoryInterface $categoryRepository
    ) {}

    public function execute(CreateCategoryInputDTO $input): CreateCategoryOutputDTO
    {
        $categoryEntity = new Category(
            name: $input->name,
            description: $input->description,
            isActive: $input->isActive,
        );

        $category = $this->categoryRepository->insert($categoryEntity);

        return new CreateCategoryOutputDTO(
            id: $category->id(),
            name: $category->name,
            description: $category->description,
            is_active: $category->isActive,
            created_at: $category->createdAt(),
        );
    }
}