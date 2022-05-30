<?php

namespace Core\UseCase\Category;

use Core\Domain\Entity\Category;
use Core\Domain\Repository\CategoryRepositoryInterface;

class CreateCategoryUseCase
{
    public function __construct(
        protected readonly CategoryRepositoryInterface $categoryRepository
    ) {}

    public function execute()
    {
        $category = new Category(
            name: 'Category name'
        );

        $this->categoryRepository->insert($category);
    }
}