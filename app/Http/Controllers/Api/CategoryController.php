<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use Core\UseCase\Category\CreateCategoryUseCase;
use Core\UseCase\Category\DeleteCategoryUseCase;
use Core\UseCase\Category\FindCategoryUseCase;
use Core\UseCase\Category\ListCategoriesUseCase;
use Core\UseCase\Category\UpdateCategoryUseCase;
use Core\UseCase\DTO\Category\CategoryInputDTO;
use Core\UseCase\DTO\Category\create\CreateCategoryInputDTO;
use Core\UseCase\DTO\Category\list\ListCategoriesInputDTO;
use Core\UseCase\DTO\Category\update\UpdateCategoryInputDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function index(
        Request $request,
        ListCategoriesUseCase $listCategoriesUseCase
    ): AnonymousResourceCollection {
        $listCategoriesInputDTO = new ListCategoriesInputDTO(
            filter: $request->get('filter', ''),
            order: $request->get('order', 'DESC'),
            page: (int) $request->get('page', 1),
            totalPage: (int) $request->get('totalPage', 15)
        );

        $categories = $listCategoriesUseCase->execute(input: $listCategoriesInputDTO);

        return CategoryResource::collection(collect($categories->items))
            ->additional([
                'meta' => [
                    'total' => $categories->total,
                    'currentPage' => $categories->currentPage,
                    'firstPage' => $categories->firstPage,
                    'lastPage' => $categories->lastPage,
                    'perPage' => $categories->perPage,
                    'to' => $categories->to,
                    'from' => $categories->from,
                ]
            ]);
    }

    public function store(
        StoreCategoryRequest $request,
        CreateCategoryUseCase $createCategoryUseCase
    ): JsonResponse {
        $createCategoryInputDTO = new CreateCategoryInputDTO(
            name: $request->name,
            description: $request->get('description', ''),
            isActive: (bool) $request->get('is_active', true),
        );

        $category = $createCategoryUseCase->execute(input: $createCategoryInputDTO);

        return (new CategoryResource(collect($category)))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(
        FindCategoryUseCase $findCategoryUseCase,
        string $id
    ): JsonResponse {
        $categoryInputDTO = new CategoryInputDTO(id: $id);

        $category = $findCategoryUseCase->execute($categoryInputDTO);

        return (new CategoryResource(collect($category)))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function update(
        UpdateCategoryRequest $request,
        UpdateCategoryUseCase $updateCategoryUseCase,
        string $id
    ): JsonResponse {
        $updateCategoryInputDTO = new UpdateCategoryInputDTO(
            id: $id,
            name: $request->name,
            description: $request->get('description', ''),
            isActive: (bool) $request->get('is_active', true),
        );

        $category = $updateCategoryUseCase->execute(input: $updateCategoryInputDTO);

        return (new CategoryResource(collect($category)))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function destroy(
        DeleteCategoryUseCase $deleteCategoryUseCase,
        string $id
    ): Response {
        $categoryInputDTO = new CategoryInputDTO(id: $id);

        $deleteCategoryUseCase->execute($categoryInputDTO);

        return response()->noContent();
    }
}
