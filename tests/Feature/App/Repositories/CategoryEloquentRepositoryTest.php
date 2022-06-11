<?php

namespace Tests\Feature\App\Repositories;

use App\Models\Category as CategoryModel;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\Domain\Entity\Category as CategoryEntity;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Tests\TestCase;
use Throwable;

class CategoryEloquentRepositoryTest extends TestCase
{
    protected $categoryEloquentRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryEloquentRepository =
            new CategoryEloquentRepository(new CategoryModel());
    }

    public function testInsert()
    {
        $categoryEntity = new CategoryEntity(
            name: 'Category name'
        );

        $response = $this->categoryEloquentRepository->insert($categoryEntity);

        $this->assertInstanceOf(
            CategoryRepositoryInterface::class,
            $this->categoryEloquentRepository
        );
        $this->assertInstanceOf(CategoryEntity::class, $response);
        $this->assertDatabaseHas('categories', ['name' => 'Category name']);
    }

    public function findById()
    {
        $categoryModel = CategoryModel::factory()->create();

        $response = $this->categoryEloquentRepository->findById($categoryModel->id);

        $this->assertInstanceOf(CategoryEntity::class, $response);
        $this->assertEquals($categoryModel->id, $response->id());
    }

    public function testFindByIdNotFound()
    {
        try {
            $this->categoryEloquentRepository->findById('categoryId');

            $this->assertTrue(false);
        } catch (Throwable $throwable) {
            $this->assertInstanceOf(NotFoundException::class, $throwable);
        }
    }
}
