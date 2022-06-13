<?php

namespace App\Repositories\Eloquent;

use App\Models\Category as CategoryModel;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Entity\Category as CategoryEntity;
use Core\Domain\Exception\NotFoundException;
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
        $category = $this->categoryModel->find($id);
        if (! $category) throw new NotFoundException("Category with id: $id not found");

        return $this->toCategory($category);
    }

    public function getIdsByListId(array $categoriesId): array
    {
        return $this->categoryModel->whereIn('id', $categoriesId)->get()->toArray();
    }

    public function findAll(string $filter = '', string $order = 'DESC'): array
    {
        $categories = $this->categoryModel
            ->where(function($query) use ($filter) {
                if ($filter) $query->where('name', 'ILIKE', "%$filter%");
            })
            ->orderBy('id', $order)
            ->get();

        return $categories->toArray();
    }

    public function paginate(
        string $filter = '',
        string $order = 'DESC',
        int $page = 1,
        int $totalPage = 15
    ): PaginationInterface {
        $categories = $this->categoryModel
            ->where(function($query) use ($filter) {
                if ($filter) $query->where('name', 'ILIKE', "%$filter%");
            })
            ->orderBy('id', $order)
            ->paginate();

        return new PaginationPresenter($categories);
    }

    public function update(CategoryEntity $categoryEntity): CategoryEntity
    {
        $category = $this->categoryModel->find($categoryEntity->id());
        if (! $category) {
            throw new NotFoundException("Category with id: {$categoryEntity->id()} not found");
        }

        $category->update([
            'name' =>$categoryEntity->name,
            'description' =>$categoryEntity->description,
            'is_active' =>$categoryEntity->isActive,
        ]);
        $category->refresh();

        return $this->toCategory($category);
    }

    public function delete(string $id): bool
    {
        $category = $this->categoryModel->find($id);
        if (! $category) throw new NotFoundException("Category with id: $id not found");

        return $category->delete();
    }

    public function toCategory(object $data): CategoryEntity
    {
        $categoryEntity = new CategoryEntity(
            id: $data->id,
            name: $data->name,
            description: $data->description,
            createdAt: $data->created_at,
        );

        $data->is_active ? $categoryEntity->activate() : $categoryEntity->disable();

        return $categoryEntity;
    }
}
