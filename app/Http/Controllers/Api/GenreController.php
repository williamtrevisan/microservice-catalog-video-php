<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGenreRequest;
use App\Http\Requests\UpdateGenreRequest;
use App\Http\Resources\GenreResource;
use Core\UseCase\DTO\Genre\Create\CreateGenreInputDTO;
use Core\UseCase\DTO\Genre\GenreInputDTO;
use Core\UseCase\DTO\Genre\List\ListGenresInputDTO;
use Core\UseCase\DTO\Genre\Update\UpdateGenreInputDTO;
use Core\UseCase\Genre\CreateGenreUseCase;
use Core\UseCase\Genre\DeleteGenreUseCase;
use Core\UseCase\Genre\FindGenreUseCase;
use Core\UseCase\Genre\ListGenresUseCase;
use Core\UseCase\Genre\UpdateGenreUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class GenreController extends Controller
{
    public function index(
        Request $request,
        ListGenresUseCase $listGenresUseCase
    ): AnonymousResourceCollection {
        $listGenresInputDTO = new ListGenresInputDTO(
            filter: $request->get('filter', ''),
            order: $request->get('order', 'DESC'),
            page: (int) $request->get('page', 1),
            totalPage: (int) $request->get('totalPage', 15)
        );

        $categories = $listGenresUseCase->execute(input: $listGenresInputDTO);

        return GenreResource::collection(collect($categories->items))
            ->additional([
                'meta' => [
                    'total' => $categories->total,
                    'current_page' => $categories->current_page,
                    'first_page' => $categories->first_page,
                    'last_page' => $categories->last_page,
                    'per_page' => $categories->per_page,
                    'to' => $categories->to,
                    'from' => $categories->from,
                ]
            ]);
    }

    public function store(
        StoreGenreRequest $request,
        CreateGenreUseCase $createGenreUseCase
    ): JsonResponse {
        $createGenreInputDTO = new CreateGenreInputDTO(
            name: $request->name,
            categoriesId: $request->categories_id,
            isActive: (bool) $request->get('is_active', true),
        );

        $genre = $createGenreUseCase->execute(input: $createGenreInputDTO);

        return (new GenreResource($genre))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(
        FindGenreUseCase $findGenreUseCase,
        string $id
    ): JsonResponse {
        $genreInputDTO = new GenreInputDTO(id: $id);

        $genre = $findGenreUseCase->execute(input: $genreInputDTO);

        return (new GenreResource($genre))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function update(
        UpdateGenreRequest $request,
        UpdateGenreUseCase $updateGenreUseCase,
        string $id
    ): JsonResponse {
        $updateGenreInputDTO = new UpdateGenreInputDTO(
            id: $id,
            name: $request->name,
            categoriesId: $request->categories_id,
        );

        $genre = $updateGenreUseCase->execute(input: $updateGenreInputDTO);

        return (new GenreResource($genre))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function destroy(
        DeleteGenreUseCase $deleteGenreUseCase,
        string $id
    ): Response {
        $genreInputDTO = new GenreInputDTO(id: $id);

        $deleteGenreUseCase->execute(input: $genreInputDTO);

        return response()->noContent();
    }
}
