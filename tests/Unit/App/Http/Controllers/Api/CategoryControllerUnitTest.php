<?php

namespace Tests\Unit\App\Http\Controllers\Api;

use App\Http\Controllers\Api\CategoryController;
use Core\UseCase\Category\ListCategoriesUseCase;
use Core\UseCase\DTO\Category\list\ListCategoriesOutputDTO;
use Illuminate\Http\Request;
use Mockery;
use PHPUnit\Framework\TestCase;

class CategoryControllerUnitTest extends TestCase
{
    public function testIndex()
    {
        $request = Mockery::mock(Request::class);
        $request->shouldReceive('get')->andReturn('teste');
        $listCategoriesOutputDTO =
            Mockery::mock(ListCategoriesOutputDTO::class, [[], 1, 1, 1, 1, 1, 1, 1]);
        $listCategoriesUseCase = Mockery::mock(ListCategoriesUseCase::class);
        $listCategoriesUseCase
            ->shouldReceive('execute')
            ->andReturn($listCategoriesOutputDTO);

        $categoryController = new CategoryController();
        $response = $categoryController->index($request, $listCategoriesUseCase);

        $listCategoriesUseCase->shouldHaveReceived('execute');
        $this->assertIsObject($response->resource);
        $this->assertArrayHasKey('meta', $response->additional);

        Mockery::close();
    }
}
