<?php

namespace Api;

use App\Models\CastMember as CastMemberModel;
use Core\Domain\Enum\CastMemberType;
use Illuminate\Http\Response;
use Tests\TestCase;

class CastMemberApiTest extends TestCase
{
    const DIRECTOR = 1;
    const ACTOR = 2;

    protected string $endpoint = '/api/cast_members';

    public function testShouldBeAbleToListWithoutCastMembersCreated()
    {
        $response = $this->getJson($this->endpoint);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(0, 'data');
    }

    public function testShouldBeAbleToListAllCastMembers()
    {
        CastMemberModel::factory(30)->create();

        $response = $this->getJson($this->endpoint);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(15, 'data');
        $response->assertJsonStructure([
            'meta' => [
                'total',
                'current_page',
                'first_page',
                'last_page',
                'per_page',
                'to',
                'from',
            ]
        ]);
    }

    public function testShouldBeAbleToListCorrectlyCastMembersOnPageTwo()
    {
        CastMemberModel::factory(23)->create();

        $response = $this->getJson("$this->endpoint?page=2");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(8, 'data');
        $this->assertEquals(23, $response['meta']['total']);
        $this->assertEquals(2, $response['meta']['current_page']);
    }

    public function testShouldBeAbleToListCorrectlyCastMembersFilteringByName()
    {
        CastMemberModel::factory(13)->create();
        CastMemberModel::factory(1)->create([
            'name' => 'Cast member name'
        ]);

        $response = $this->getJson("$this->endpoint?filter=Cast%20member%20name");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonCount(1, 'data');
    }

    public function testShouldBeReturnAnExceptionIfNameIsEmpty()
    {
        $payload = ['type' => self::DIRECTOR];

        $response = $this->postJson($this->endpoint, $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => ['name']
        ]);
    }

    public function testShouldBeReturnAnExceptionIfTypeIsEmpty()
    {
        $payload = ['name' => 'Cast member name'];

        $response = $this->postJson($this->endpoint, $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => ['type']
        ]);
    }

    public function testShouldBeReturnAnExceptionIfNameAndTypeAreEmpty()
    {
        $payload = [];

        $response = $this->postJson($this->endpoint, $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => ['name', 'type']
        ]);
    }

    public function testShouldBeAbleToCreateANewCastMember()
    {
        $payload = [
            'name' => 'Cast member name',
            'type' => self::ACTOR,
        ];

        $response = $this->postJson($this->endpoint, $payload);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'type',
                'created_at'
            ],
        ]);
        $this->assertEquals($payload['name'], $response['data']['name']);
        $this->assertEquals(self::ACTOR, $response['data']['type']);
    }

    public function testShouldBeReturnNotFoundIfReceivedAInvalidIdOnShowRoute()
    {
        $response = $this->getJson("$this->endpoint/castMemberId");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShouldBeAbleToFindACastMemberById()
    {
        $castMember = CastMemberModel::factory()->create();

        $response = $this->getJson("$this->endpoint/$castMember->id");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'type',
                'created_at',
            ],
        ]);
        $this->assertEquals($castMember->id, $response['data']['id']);
    }

    public function testShouldBeReturnAnExceptionIfNameIsEmptyOnUpdateRoute()
    {
        $castMember = CastMemberModel::factory()->create();
        $payload = [];

        $response = $this->putJson("$this->endpoint/$castMember->id", $payload);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => ['name']
        ]);
    }

    public function testShouldBeReturnNotFoundIfReceivedAInvalidIdOnUpdateRoute()
    {
        $payload = [
            'name' => 'Cast member name',
            'type' => self::DIRECTOR,
        ];

        $response = $this->putJson("$this->endpoint/castMemberId", $payload);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShouldBeAbleToUpdateACastMember()
    {
        $castMember = CastMemberModel::factory()->create();
        $payload = [
            'name' => 'Cast member name updated',
            'type' => self::ACTOR,
        ];

        $response = $this->putJson("$this->endpoint/$castMember->id", $payload);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'type',
                'created_at',
            ],
        ]);
        $this->assertEquals($castMember->id, $response['data']['id']);
    }

    public function testShouldBeReturnNotFoundIfReceivedAInvalidIdOnDeleteRoute()
    {
        $response = $this->deleteJson("$this->endpoint/castMemberId");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShouldBeAbleToDeleteACastMember()
    {
        $castMember = CastMemberModel::factory()->create();

        $response = $this->deleteJson("$this->endpoint/$castMember->id");

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
