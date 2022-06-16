<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreCastMemberRequest;
use App\Http\Requests\UpdateCastMemberRequest;
use App\Models\CastMember as CastMemberModel;
use App\Repositories\Eloquent\CastMemberEloquentRepository;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\CastMember\CreateCastMemberUseCase;
use Core\UseCase\CastMember\DeleteCastMemberUseCase;
use Core\UseCase\CastMember\FindCastMemberUseCase;
use Core\UseCase\CastMember\ListCastMembersUseCase;
use Core\UseCase\CastMember\UpdateCastMemberUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\ParameterBag;
use Tests\TestCase;

class CastMemberControllerTest extends TestCase
{
    protected CastMemberController $castMemberController;
    protected CastMemberRepositoryInterface $castMemberRepository;

    protected function setUp(): void
    {
        $this->castMemberController = new CastMemberController();
        $this->castMemberRepository = new CastMemberEloquentRepository(
            castMemberModel: new CastMemberModel()
        );

        parent::setUp();
    }

    public function testShouldBeAbleToGetAllCastMembersPaginated()
    {
        $listCastMembersUseCase = new ListCastMembersUseCase($this->castMemberRepository);
        $request = new Request();

        $response = $this->castMemberController->index($request, $listCastMembersUseCase);

        $this->assertInstanceOf(AnonymousResourceCollection::class, $response);
        $this->assertArrayHasKey('meta', $response->additional);
    }

    public function testShouldBeAbleToCreateANewCastMember()
    {
        $createCastMemberUseCase = new CreateCastMemberUseCase($this->castMemberRepository);
        $request = new StoreCastMemberRequest();
        $request->headers->set('Content-Type', 'application/json');
        $request->setJson(new ParameterBag([
            'name' => 'Cast member name',
            'type' => 1
        ]));

        $response = $this->castMemberController->store($request, $createCastMemberUseCase);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_CREATED, $response->status());
    }

    public function tesShouldBeAbleToFindACastMemberById()
    {
        $castMember = CastMemberModel::factory()->create();
        $findCastMemberUseCase = new FindCastMemberUseCase(
            castMemberRepository: $this->castMemberRepository
        );

        $response = $this->castMemberController->show(
            findCastMemberUseCase: $findCastMemberUseCase,
            id: $castMember->id,
        );

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->status());
    }

    public function testShouldBeAbleToUpdateACastMember()
    {
        $castMember = CastMemberModel::factory()->create();
        $updateCastMemberUseCase = new UpdateCastMemberUseCase(
            castMemberRepository: $this->castMemberRepository
        );
        $request = new UpdateCastMemberRequest();
        $request->headers->set('Content-Type', 'application/json');
        $request->setJson(new ParameterBag(['name' => 'Cast member name updated']));

        $response = $this->castMemberController->update(
            request: $request,
            updateCastMemberUseCase: $updateCastMemberUseCase,
            id: $castMember->id
        );

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertDatabaseHas('cast_members', [
            'name' => 'Cast member name updated'
        ]);
    }

    public function testShouldBeAbleToDeleteACastMember()
    {
        $castMember = CastMemberModel::factory()->create();
        $deleteCastMemberUseCase = new DeleteCastMemberUseCase(
            categoryRepository: $this->castMemberRepository
        );

        $response = $this->castMemberController->destroy(
            deleteCastMemberUseCase: $deleteCastMemberUseCase,
            id: $castMember->id
        );

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->status());
    }
}
