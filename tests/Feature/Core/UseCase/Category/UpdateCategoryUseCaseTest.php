<?php

namespace Tests\Feature\Core\UseCase\Category;

use App\Models\Category as CategoryModel;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\UseCase\Category\UpdateCategoryUseCase;
use Core\UseCase\DTO\Category\Update\UpdateCategoryInputDTO;
use Core\UseCase\DTO\Category\Update\UpdateCategoryOutputDTO;
use Tests\TestCase;

class UpdateCategoryUseCaseTest extends TestCase
{
    public function testUpdate()
    {
        $categoryDatabase = CategoryModel::factory()->create();
        $categoryRepository = new CategoryEloquentRepository(new CategoryModel());
        $updateCategoryInputDTO = new UpdateCategoryInputDTO(
            id: $categoryDatabase->id,
            name: 'Category name updated'
        );

        $updateCategoryUseCase = new UpdateCategoryUseCase($categoryRepository);
        $response = $updateCategoryUseCase->execute($updateCategoryInputDTO);

        $this->assertInstanceOf(UpdateCategoryOutputDTO::class, $response);
        $this->assertEquals('Category name updated', $response->name);
        $this->assertEquals($categoryDatabase->description, $response->description);
        $this->assertDatabaseHas('categories', ['name' => 'Category name updated']);
    }
}
