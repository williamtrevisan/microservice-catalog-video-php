<?php

namespace Tests\Unit\UseCase\Category;

use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\Category\DeleteCategoryUseCase;
use Core\UseCase\DTO\Category\CategoryInputDTO;
use Core\UseCase\DTO\Category\delete\DeleteCategoryOutputDTO;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class DeleteCategoryUseCaseUnitTest extends TestCase
{
    public function testDelete()
    {
        $categoryId = Uuid::uuid4()->toString();
        $categoryRepository =
            Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $categoryRepository->shouldReceive('delete')->andReturn(true);
        $categoryInputDTO = Mockery::mock(CategoryInputDTO::class, [
            $categoryId,
        ]);

        $findCategoryUseCase = new DeleteCategoryUseCase($categoryRepository);
        $response = $findCategoryUseCase->execute($categoryInputDTO);

        $categoryRepository->shouldHaveReceived('delete');
        $this->assertInstanceOf(DeleteCategoryOutputDTO::class, $response);
        $this->assertTrue($response->success);
    }
}