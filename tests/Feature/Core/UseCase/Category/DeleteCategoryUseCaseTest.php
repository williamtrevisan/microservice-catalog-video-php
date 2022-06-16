<?php

namespace Core\UseCase\Category;

use App\Models\Category as CategoryModel;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\UseCase\Category\DeleteCategoryUseCase;
use Core\UseCase\DTO\Category\CategoryInputDTO;
use Core\UseCase\DTO\Category\Delete\DeleteCategoryOutputDTO;
use Tests\TestCase;

class DeleteCategoryUseCaseTest extends TestCase
{
    public function testDelete()
    {
        $categoryDatabase = CategoryModel::factory()->create();
        $categoryRepository = new CategoryEloquentRepository(new CategoryModel());
        $categoryInputDTO = new CategoryInputDTO(id: $categoryDatabase->id);

        $deleteCategoryUseCase = new DeleteCategoryUseCase($categoryRepository);
        $response = $deleteCategoryUseCase->execute($categoryInputDTO);

        $this->assertInstanceOf(DeleteCategoryOutputDTO::class, $response);
        $this->assertSoftDeleted($categoryDatabase);
    }
}
