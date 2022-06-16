<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCastMemberRequest;
use App\Http\Requests\UpdateCastMemberRequest;
use App\Http\Resources\CastMemberResource;
use Core\UseCase\CastMember\CreateCastMemberUseCase;
use Core\UseCase\CastMember\DeleteCastMemberUseCase;
use Core\UseCase\CastMember\FindCastMemberUseCase;
use Core\UseCase\CastMember\ListCastMembersUseCase;
use Core\UseCase\CastMember\UpdateCastMemberUseCase;
use Core\UseCase\DTO\CastMember\CastMemberInputDTO;
use Core\UseCase\DTO\CastMember\Create\CreateCastMemberInputDTO;
use Core\UseCase\DTO\CastMember\List\ListCastMembersInputDTO;
use Core\UseCase\DTO\CastMember\Update\UpdateCastMemberInputDTO;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CastMemberController extends Controller
{
    public function index(
        Request $request,
        ListCastMembersUseCase $listCastMembersUseCase
    ): AnonymousResourceCollection {
        $listCastMembersInputDTO = new ListCastMembersInputDTO(
            filter: $request->get('filter', ''),
            order: $request->get('order', 'DESC'),
            page: (int) $request->get('page', 1),
            totalPage: (int) $request->get('totalPage', 15)
        );

        $castMembers = $listCastMembersUseCase->execute(input: $listCastMembersInputDTO);

        return CastMemberResource::collection(collect($castMembers->items))
            ->additional([
                'meta' => [
                    'total' => $castMembers->total,
                    'current_page' => $castMembers->current_page,
                    'first_page' => $castMembers->first_page,
                    'last_page' => $castMembers->last_page,
                    'per_page' => $castMembers->per_page,
                    'to' => $castMembers->to,
                    'from' => $castMembers->from,
                ]
            ]);
    }

    public function store(
        StoreCastMemberRequest $request,
        CreateCastMemberUseCase $createCastMemberUseCase
    ): JsonResponse {
        $createCastMemberInputDTO = new CreateCastMemberInputDTO(
            name: $request->name,
            type: $request->type,
        );

        $castMember = $createCastMemberUseCase->execute(input: $createCastMemberInputDTO);

        return (new CastMemberResource($castMember))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(
        FindCastMemberUseCase $findCastMemberUseCase,
        string $id
    ): JsonResponse {
        $castMemberInputDTO = new CastMemberInputDTO(id: $id);

        $castMember = $findCastMemberUseCase->execute(input: $castMemberInputDTO);

        return (new CastMemberResource($castMember))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function update(
        UpdateCastMemberRequest $request,
        UpdateCastMemberUseCase $updateCastMemberUseCase,
        string $id
    ): JsonResponse {
        $updateCastMemberInputDTO = new UpdateCastMemberInputDTO(
            id: $id,
            name: $request->name,
        );

        $castMember = $updateCastMemberUseCase->execute(input: $updateCastMemberInputDTO);

        return (new CastMemberResource($castMember))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    public function destroy(
        DeleteCastMemberUseCase $deleteCastMemberUseCase,
        string $id
    ): Response {
        $castMemberInputDTO = new CastMemberInputDTO(id: $id);

        $deleteCastMemberUseCase->execute(input: $castMemberInputDTO);

        return response()->noContent();
    }
}
