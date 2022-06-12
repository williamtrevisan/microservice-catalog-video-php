<?php

namespace Tests\Feature\Core\UseCase\Category;

use App\Models\Category as CategoryModel;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\UseCase\Category\FindCategoryUseCase;
use Core\UseCase\DTO\Category\CategoryInputDTO;
use Core\UseCase\DTO\Category\find\FindCategoryOutputDTO;
use Tests\TestCase;

class FindCategoryUseCaseTest extends TestCase
{
    public function testFind()
    {
        $categoryDatabase = CategoryModel::factory()->create();
        $categoryRepository = new CategoryEloquentRepository(new CategoryModel());
        $categoryInputDTO = new CategoryInputDTO(id: $categoryDatabase->id);

        $findCategoryUseCase = new FindCategoryUseCase($categoryRepository);
        $response = $findCategoryUseCase->execute($categoryInputDTO);

        $this->assertInstanceOf(FindCategoryOutputDTO::class, $response);
        $this->assertEquals($categoryDatabase->id, $response->id);
        $this->assertEquals($categoryDatabase->name, $response->name);
        $this->assertEquals($categoryDatabase->description, $response->description);
    }
}
