<?php

namespace Tests\Feature\App\Http\Controllers\Api;

use App\Http\Controllers\Api\CategoryController;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\UseCase\Category\CreateCategoryUseCase;
use Core\UseCase\Category\FindCategoryUseCase;
use Core\UseCase\Category\ListCategoriesUseCase;
use Core\UseCase\Category\UpdateCategoryUseCase;
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
        $request = new Request();

        $response = $this->categoryController->index($request, $listCategoriesUseCase);

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

    public function testShow()
    {
        $category = Category::factory()->create();
        $findCategoryUseCase = new FindCategoryUseCase($this->categoryRepository);

        $response = $this->categoryController->show(
            findCategoryUseCase: $findCategoryUseCase,
            id: $category->id,
        );

        $this->assertEquals(Response::HTTP_OK, $response->status());
    }

    public function testUpdate()
    {
        $category = Category::factory()->create();
        $updateCategoryUseCase = new UpdateCategoryUseCase($this->categoryRepository);
        $request = new UpdateCategoryRequest();
        $request->headers->set('Content-Type', 'application/json');
        $request->setJson(new ParameterBag(['name' => 'Category name updated']));

        $response = $this->categoryController->update(
            request: $request,
            updateCategoryUseCase: $updateCategoryUseCase,
            id: $category->id
        );

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertDatabaseHas('categories', [
            'name' => 'Category name updated'
        ]);
    }
}
