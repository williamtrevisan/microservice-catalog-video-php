<?php

namespace App\Repositories\Eloquent;

use App\Models\Category as CategoryModel;
use Core\Domain\Entity\Category as CategoryEntity;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;

class CategoryEloquentRepository implements CategoryRepositoryInterface
{
    public function __construct(protected readonly CategoryModel $categoryModel) {}

    public function insert(CategoryEntity $categoryEntity): CategoryEntity
    {
        $category = $this->categoryModel->create([
            'id' => $categoryEntity->id(),
            'name' => $categoryEntity->name,
            'description' => $categoryEntity->description,
            'is_active' => $categoryEntity->isActive,
            'created_at' => $categoryEntity->createdAt(),
        ]);

        return $this->toCategory($category);
    }

    public function findById(string $id): CategoryEntity
    {
        // TODO: Implement findById() method.
    }

    public function findAll(string $filter = '', string $order = 'DESC'): array
    {
        // TODO: Implement findAll() method.
    }

    public function paginate(string $filter = '', string $order = 'DESC', int $page = 1, int $totalPage = 15): PaginationInterface
    {
        // TODO: Implement paginate() method.
    }

    public function update(CategoryEntity $categoryEntity): CategoryEntity
    {
        // TODO: Implement update() method.
    }

    public function delete(string $id): bool
    {
        // TODO: Implement delete() method.
    }

    public function toCategory(object $data): CategoryEntity
    {
        return new CategoryEntity(
            id: $data->id,
            name: $data->name,
            description: $data->description,
            isActive: $data->is_active,
            createdAt: $data->created_at,
        );
    }
}