<?php

namespace Core\UseCase\Category;

use App\Models\Category as CategoryModel;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\UseCase\Category\CreateCategoryUseCase;
use Core\UseCase\DTO\Category\Create\CreateCategoryInputDTO;
use Core\UseCase\DTO\Category\Create\CreateCategoryOutputDTO;
use Tests\TestCase;

class CreateCategoryUseCaseTest extends TestCase
{
    public function testCreate()
    {
        $categoryRepository = new CategoryEloquentRepository(new CategoryModel());
        $createCategoryInputDTO = new CreateCategoryInputDTO(name: 'Category name');

        $createCategoryUseCase = new CreateCategoryUseCase($categoryRepository);
        $response = $createCategoryUseCase->execute($createCategoryInputDTO);

        $this->assertInstanceOf(CreateCategoryOutputDTO::class, $response);
        $this->assertNotEmpty($response->id);
        $this->assertEquals('Category name', $response->name);
        $this->assertDatabaseHas('categories', ['id' => $response->id]);
    }
}
