<?php

namespace Tests\Unit\Domain\UseCase\Category;

use Core\Domain\Entity\Category;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\Category\CreateCategoryUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class CreateCategoryUseCaseUnitTest extends TestCase
{
    public function testCreateNewCategory()
    {
        $categoryId = '1';
        $categoryName = 'Category name';
        $categoryEntity = Mockery::mock(Category::class, [$categoryId, $categoryName]);
        $categoryRepository =
            Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $categoryRepository->shouldReceive('insert')->andReturn($categoryEntity);

        $createCategoryUseCase = new CreateCategoryUseCase($categoryRepository);
        $createCategoryUseCase->execute();
    }
}