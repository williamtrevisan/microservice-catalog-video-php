<?php

namespace Core\UseCase\Category;

use App\Models\Category as CategoryModel;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\UseCase\Category\ListCategoriesUseCase;
use Core\UseCase\DTO\Category\List\ListCategoriesInputDTO;
use Core\UseCase\DTO\Category\List\ListCategoriesOutputDTO;
use Tests\TestCase;

class ListCategoriesUseCaseTest extends TestCase
{
    public function testListCategories()
    {
        CategoryModel::factory()->count(30)->create();
        $categoryRepository = new CategoryEloquentRepository(new CategoryModel());
        $listCategoriesInputDTO = new ListCategoriesInputDTO();

        $listCategoriesUseCase = new ListCategoriesUseCase($categoryRepository);
        $response = $listCategoriesUseCase->execute($listCategoriesInputDTO);

        $this->assertInstanceOf(ListCategoriesOutputDTO::class, $response);
        $this->assertCount(15, $response->items);
        $this->assertEquals(30, $response->total);
    }

    public function testListCategoriesEmpty()
    {
        $categoryRepository = new CategoryEloquentRepository(new CategoryModel());
        $listCategoriesInputDTO = new ListCategoriesInputDTO();

        $listCategoriesUseCase = new ListCategoriesUseCase($categoryRepository);
        $response = $listCategoriesUseCase->execute($listCategoriesInputDTO);

        $this->assertInstanceOf(ListCategoriesOutputDTO::class, $response);
        $this->assertCount(0, $response->items);
    }
}
