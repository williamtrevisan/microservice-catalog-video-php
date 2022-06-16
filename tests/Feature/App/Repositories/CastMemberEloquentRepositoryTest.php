<?php

namespace App\Repositories;

use App\Models\CastMember as CastMemberModel;
use App\Repositories\Eloquent\CastMemberEloquentRepository;
use Core\Domain\Entity\CastMember as CastMemberEntity;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use DateTime;
use Tests\TestCase;

class CastMemberEloquentRepositoryTest extends TestCase
{
    protected CastMemberRepositoryInterface $castMemberEloquentRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->castMemberEloquentRepository = new CastMemberEloquentRepository(
            castMemberModel: new CastMemberModel()
        );
    }

    public function testRepositoryMustImplementsInterface()
    {
        $this->assertInstanceOf(
            CastMemberRepositoryInterface::class,
            $this->castMemberEloquentRepository
        );
    }

    public function testShouldBeAbleToCreateANewCastMember()
    {
        $castMemberEntity = new CastMemberEntity(
            name: 'Cast member name',
            type: CastMemberType::Actor
        );

        $response = $this->castMemberEloquentRepository->insert($castMemberEntity);

        $this->assertNotEmpty($response->id());
        $this->assertEquals($castMemberEntity->name, $response->name);
        $this->assertEquals(CastMemberType::Actor, $response->type);
        $this->assertNotEmpty($response->createdAt());
        $this->assertDatabaseHas('cast_members', ['id' => $castMemberEntity->id()]);
    }

    public function testShouldReturnAnExceptionIfNotFoundACastMember()
    {
        $this->expectException(NotFoundException::class);

        $this->castMemberEloquentRepository->findById('categoryId');
    }

    public function testShouldBeAbleToFindACastMemberById()
    {
        $castMember = CastMemberModel::factory()->create();

        $response = $this->castMemberEloquentRepository->findById($castMember->id);

        $this->assertEquals($castMember->id, $response->id());
        $this->assertEquals($castMember->name, $response->name);
    }

    public function testShouldBeAbleToFindAllCastMembers()
    {
        CastMemberModel::factory()->count(2)->create([
            'name' => 'Cast member name',
        ]);
        CastMemberModel::factory()->count(4)->create();

        $response = $this->castMemberEloquentRepository->findAll();

        $this->assertCount(6, $response);
    }

    public function testShouldBeAbleToFindAllCastMembersFilteringByName()
    {
        CastMemberModel::factory()->count(1)->create([
            'name' => 'Cast member name',
        ]);
        CastMemberModel::factory()->count(3)->create();

        $response = $this->castMemberEloquentRepository->findAll(filter: 'Cast member name');

        $this->assertCount(1, $response);
    }

    public function testShouldBeFindAllWithoutCastMembersCreated()
    {
        $response = $this->castMemberEloquentRepository->findAll();

        $this->assertCount(0, $response);
    }

    public function testShouldBeAbleToGetPaginateCastMembers()
    {
        CastMemberModel::factory()->count(54)->create();

        $response = $this->castMemberEloquentRepository->paginate();

        $this->assertCount(15, $response->items());
        $this->assertEquals(54, $response->total());
    }

    public function testShouldBeAbleToGetPaginateCastMembersWithoutDataCreated()
    {
        $response = $this->castMemberEloquentRepository->paginate();

        $this->assertCount(0, $response->items());
        $this->assertEquals(0, $response->total());
    }

    public function testShouldBeAbleToUpdateACastMember()
    {
        $castMember = CastMemberModel::factory()->create();
        $castMemberEntity = new CastMemberEntity(
            name: $castMember->name,
            type: $castMember->type,
            id: new Uuid($castMember->id),
            createdAt: new DateTime($castMember->created_at)
        );
        $castMemberEntity->update(name: 'Cast member name updated');

        $response = $this->castMemberEloquentRepository->update($castMemberEntity);

        $this->assertEquals('Cast member name updated', $response->name);
        $this->assertNotEquals($castMember->name, $response->name);
        $this->assertDatabaseHas('cast_members', ['name' => 'Cast member name updated']);
    }

    public function testShouldReturnAnExceptionIfNotFoundCastMemberOnDelete()
    {
        $this->expectException(NotFoundException::class);

        $this->castMemberEloquentRepository->delete("castMemberId");
    }

    public function testShouldBeAbleToDeleteACastMember()
    {
        $castMember = CastMemberModel::factory()->create();

        $response = $this->castMemberEloquentRepository->delete($castMember->id);

        $this->assertTrue($response);
        $this->assertSoftDeleted('cast_members', ['id' => $castMember->id]);
    }
}
