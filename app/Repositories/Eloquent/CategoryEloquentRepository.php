<?php

namespace App\Repositories\Eloquent;

use App\Models\Category as CategoryModel;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Entity\{BaseEntity, Category as CategoryEntity};
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\{CategoryRepositoryInterface, PaginationInterface};

class CategoryEloquentRepository implements CategoryRepositoryInterface
{
    public function __construct(protected readonly CategoryModel $categoryModel) {}

    public function insert(BaseEntity $categoryEntity): BaseEntity
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

    /**
     * @throws NotFoundException
     */
    public function findById(string $id): BaseEntity
    {
        $category = $this->categoryModel->find($id);
        if (! $category) throw new NotFoundException("Category with id: $id not found");

        return $this->toCategory($category);
    }

    public function getIdsByListId(array $categoriesId = []): array
    {
        return $this->categoryModel
            ->whereIn('id', $categoriesId)
            ->pluck('id')
            ->toArray();
    }

    public function findAll(string $filter = '', string $order = 'DESC'): array
    {
        $categories = $this->categoryModel
            ->where(function($query) use ($filter) {
                if ($filter) $query->where('name', 'LIKE', "%$filter%");
            })
            ->orderBy('name', $order)
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
                if ($filter) $query->where('name', 'LIKE', "%$filter%");
            })
            ->orderBy('name', $order)
            ->paginate();

        return new PaginationPresenter($categories);
    }

    /**
     * @throws NotFoundException
     */
    public function update(BaseEntity $categoryEntity): BaseEntity
    {
        $category = $this->categoryModel->find($categoryEntity->id());
        if (! $category) {
            throw new NotFoundException("Category with id: {$categoryEntity->id()} not found");
        }

        $category->update([
            'name' => $categoryEntity->name,
            'description' => $categoryEntity->description,
            'is_active' => $categoryEntity->isActive,
        ]);
        $category->refresh();

        return $this->toCategory($category);
    }

    /**
     * @throws NotFoundException
     */
    public function delete(string $id): bool
    {
        $category = $this->categoryModel->find($id);
        if (! $category) throw new NotFoundException("Category with id: $id not found");

        return $category->delete();
    }

    private function toCategory(object $data): BaseEntity
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
