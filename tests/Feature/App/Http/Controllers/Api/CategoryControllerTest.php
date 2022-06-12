<?php

namespace Tests\Feature\App\Http\Controllers\Api;

use App\Http\Controllers\Api\CategoryController;
use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\UseCase\Category\CreateCategoryUseCase;
use Core\UseCase\Category\ListCategoriesUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\ParameterBag;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    protected $categoryController;
    protected $categoryRepository;

    protected function setUp(): void
    {
        $this->categoryController = new CategoryController();
        $this->categoryRepository = new CategoryEloquentRepository(new Category());

        parent::setUp();
    }

    public function testIndex()
    {
        $listCategoriesUseCase = new ListCategoriesUseCase($this->categoryRepository);

        $response = $this->categoryController->index(
            new Request(),
            $listCategoriesUseCase
        );

        $this->assertInstanceOf(AnonymousResourceCollection::class, $response);
        $this->assertArrayHasKey('meta', $response->additional);
    }

    public function testStore()
    {
        $createCategoryUseCase = new CreateCategoryUseCase($this->categoryRepository);
        $request = new StoreCategoryRequest();
        $request->headers->set('Content-Type', 'application/json');
        $request->setJson(new ParameterBag(['name' => 'Category name']));

        $response = $this->categoryController->store($request, $createCategoryUseCase);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_CREATED, $response->status());
    }
}
