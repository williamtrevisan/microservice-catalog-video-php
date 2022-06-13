<?php

namespace Tests\Unit\UseCase\Category;

use Core\Domain\Entity\Category;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\Category\FindCategoryUseCase;
use Core\UseCase\DTO\Category\CategoryInputDTO;
use Core\UseCase\DTO\Category\Find\FindCategoryOutputDTO;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class FindCategoryUseCaseUnitTest extends TestCase
{
    public function testGetById()
    {
        $categoryId = Uuid::uuid4()->toString();
        $categoryEntity = Mockery::mock(Category::class, [
            $categoryId,
            'Category name',
        ]);
        $categoryEntity->shouldReceive('id')->andReturn($categoryId);
        $categoryEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));
        $categoryRepository =
            Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $categoryRepository
            ->shouldReceive('findById')
            ->with($categoryId)
            ->andReturn($categoryEntity);
        $categoryInputDTO = Mockery::mock(CategoryInputDTO::class, [
            $categoryId,
        ]);

        $findCategoryUseCase = new FindCategoryUseCase($categoryRepository);
        $response = $findCategoryUseCase->execute($categoryInputDTO);

        $categoryRepository->shouldHaveReceived('findById');
        $this->assertInstanceOf(FindCategoryOutputDTO::class, $response);
        $this->assertEquals('Category name', $response->name);
        $this->assertEquals($categoryId, $response->id);
    }
}
