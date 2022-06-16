<?php

namespace UseCase\Category;

use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\UseCase\Category\ListCategoriesUseCase;
use Core\UseCase\DTO\Category\List\ListCategoriesInputDTO;
use Core\UseCase\DTO\Category\List\ListCategoriesOutputDTO;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class ListCategoryUseCaseUnitTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    private function getCategoryPagination(array $items = [])
    {
        $categoryPagination = Mockery::mock(stdClass::class, PaginationInterface::class);
        $categoryPagination->shouldReceive('items')->andReturn($items);
        $categoryPagination->shouldReceive('total')->andReturn(0);
        $categoryPagination->shouldReceive('currentPage')->andReturn(0);
        $categoryPagination->shouldReceive('firstPage')->andReturn(0);
        $categoryPagination->shouldReceive('lastPage')->andReturn(0);
        $categoryPagination->shouldReceive('perPage')->andReturn(0);
        $categoryPagination->shouldReceive('to')->andReturn(0);
        $categoryPagination->shouldReceive('from')->andReturn(0);

        return $categoryPagination;
    }

    public function testListCategoriesEmpty()
    {
        $categoryPagination = $this->getCategoryPagination();
        $categoryRepository =
            Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $categoryRepository->shouldReceive('paginate')->andReturn($categoryPagination);
        $listCategoriesInputDTO = Mockery::mock(ListCategoriesInputDTO::class, ['filter', 'desc']);

        $listCategoriesUseCase = new ListCategoriesUseCase($categoryRepository);
        $response = $listCategoriesUseCase->execute($listCategoriesInputDTO);

        $categoryRepository->shouldHaveReceived('paginate');
        $this->assertInstanceOf(ListCategoriesOutputDTO::class, $response);
        $this->assertCount(0, $response->items);
    }

    public function testListCategories()
    {
        $register = new stdClass();
        $register->id = 'categoryId';
        $register->name = 'Category name';
        $register->description = 'Category description';
        $register->is_active = true;
        $register->created_at = date('Y-m-d');
        $register->updated_at = date('Y-m-d');
        $register->deleted_at = null;
        $categoryPagination = $this->getCategoryPagination([$register]);
        $categoryRepository =
            Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $categoryRepository->shouldReceive('paginate')->andReturn($categoryPagination);
        $listCategoriesInputDTO = Mockery::mock(ListCategoriesInputDTO::class, ['filter', 'desc']);

        $listCategoriesUseCase = new ListCategoriesUseCase($categoryRepository);
        $response = $listCategoriesUseCase->execute($listCategoriesInputDTO);

        $categoryRepository->shouldHaveReceived('paginate');
        $this->assertInstanceOf(stdClass::class, $response->items[0]);
        $this->assertInstanceOf(ListCategoriesOutputDTO::class, $response);
        $this->assertCount(1, $response->items);
    }
}
