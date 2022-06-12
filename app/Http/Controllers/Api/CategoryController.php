<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Core\UseCase\Category\ListCategoriesUseCase;
use Core\UseCase\DTO\Category\list\ListCategoriesInputDTO;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request, ListCategoriesUseCase $listCategoriesUseCase)
    {
        $response = $listCategoriesUseCase->execute(
            input: new ListCategoriesInputDTO(
                filter: $request->get('filter', ''),
                order: $request->get('order', 'DESC'),
                page: (int) $request->get('page', 1),
                totalPage: (int) $request->get('totalPage', 15)
            )
        );

        return CategoryResource::collection(collect($response->items))
            ->additional([
                'meta' => [
                    'total' => $response->total,
                    'currentPage' => $response->currentPage,
                    'firstPage' => $response->firstPage,
                    'lastPage' => $response->lastPage,
                    'perPage' => $response->perPage,
                    'to' => $response->to,
                    'from' => $response->from,
                ]
            ]);
    }
}
