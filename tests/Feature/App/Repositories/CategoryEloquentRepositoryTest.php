<?php

namespace Tests\Feature\App\Repositories;

use App\Models\Category as CategoryModel;
use Core\Domain\Entity\Category as CategoryEntity;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Tests\TestCase;

class CategoryEloquentRepositoryTest extends TestCase
{
    public function testInsert()
    {
        $categoryEloquentRepository =
            new CategoryEloquentRepository(new CategoryModel());
        $categoryEntity = new CategoryEntity(
            name: 'Category name'
        );

        $response = $categoryEloquentRepository->insert($categoryEntity);

        $this->assertInstanceOf(
            CategoryRepositoryInterface::class,
            $categoryEloquentRepository
        );
        $this->assertInstanceOf(CategoryEntity::class, $response);
        $this->assertDatabaseHas('categories', ['name' => 'Category name']);
    }
}
