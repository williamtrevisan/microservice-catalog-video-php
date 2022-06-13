<?php

namespace Tests\Unit\UseCase\Category;

use Core\Domain\Entity\Category;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\Category\UpdateCategoryUseCase;
use Core\UseCase\DTO\Category\Update\UpdateCategoryInputDTO;
use Core\UseCase\DTO\Category\Update\UpdateCategoryOutputDTO;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class UpdateCategoryUseCaseUnitTest extends TestCase
{
    public function testRenameCategory()
    {
        $categoryId = Uuid::uuid4()->toString();
        $categoryName = 'Category name';
        $categoryEntity = Mockery::mock(Category::class, [
            $categoryId,
            $categoryName,
        ]);
        $categoryEntity->shouldReceive('id')->andReturn($categoryId);
        $categoryEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));
        $categoryEntity->shouldReceive('update');
        $categoryRepository =
            Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $categoryRepository->shouldReceive('findById')->andReturn($categoryEntity);
        $categoryRepository->shouldReceive('update')->andReturn($categoryEntity);
        $updateCategoryInputDTO = Mockery::mock(UpdateCategoryInputDTO::class, [
            $categoryId,
            'Category name updated'
        ]);

        $updateCategoryUseCase = new UpdateCategoryUseCase($categoryRepository);
        $response = $updateCategoryUseCase->execute($updateCategoryInputDTO);

        $categoryRepository->shouldHaveReceived('findById');
        $categoryRepository->shouldHaveReceived('update');
        $categoryEntity->shouldHaveReceived('update');
        $this->assertInstanceOf(UpdateCategoryOutputDTO::class, $response);

        Mockery::close();
    }
}
