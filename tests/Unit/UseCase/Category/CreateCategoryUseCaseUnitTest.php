<?php

namespace Tests\Unit\UseCase\Category;

use Core\Domain\Entity\Category;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\Category\CreateCategoryUseCase;
use Core\UseCase\DTO\Category\create\CreateCategoryInputDTO;
use Core\UseCase\DTO\Category\create\CreateCategoryOutputDTO;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class CreateCategoryUseCaseUnitTest extends TestCase
{
    public function testCreateNewCategory()
    {
        $categoryId = Uuid::uuid4()->toString();
        $categoryName = 'Category name';
        $categoryEntity = Mockery::mock(Category::class, [
            $categoryId,
            $categoryName,
        ]);
        $categoryEntity->shouldReceive('id')->andReturn($categoryId);
        $categoryRepository =
            Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $categoryRepository->shouldReceive('insert')->andReturn($categoryEntity);
        $createCategoryInputDTO = Mockery::mock(CreateCategoryInputDTO::class, [
            $categoryName,
        ]);

        $createCategoryUseCase = new CreateCategoryUseCase($categoryRepository);
        $response = $createCategoryUseCase->execute($createCategoryInputDTO);

        $categoryRepository->shouldHaveReceived('insert');
        $this->assertInstanceOf(CreateCategoryOutputDTO::class, $response);

        Mockery::close();
    }
}