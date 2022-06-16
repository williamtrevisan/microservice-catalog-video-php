<?php

namespace UseCase\CastMember;

use Core\Domain\Entity\CastMember as CastMemberEntity;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\CastMember\CreateCastMemberUseCase;
use Core\UseCase\DTO\CastMember\Create\CreateCastMemberInputDTO;
use Core\UseCase\DTO\CastMember\Create\CreateCastMemberOutputDTO;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;
use stdClass;

class CreateCastMemberUseCaseUnitTest extends TestCase
{
    const ACTOR = 2;

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function testShouldBeAbleToCreateANewCastMember()
    {
        $castMemberId = RamseyUuid::uuid4()->toString();
        $castMemberCreatedAt = date('Y-m-d H:i:s');
        $castMemberEntity = Mockery::mock(CastMemberEntity::class, [
            'Cast member name',
            CastMemberType::Actor,
        ]);
        $castMemberEntity->shouldReceive('id')->once()->andReturn($castMemberId);
        $castMemberEntity
            ->shouldReceive('createdAt')
            ->once()
            ->andReturn($castMemberCreatedAt);
        $castMemberRepository = Mockery::mock(
            stdClass::class,
            CastMemberRepositoryInterface::class
        );
        $castMemberRepository
            ->shouldReceive('insert')
            ->once()
            ->andReturn($castMemberEntity);
        $createCastMemberInputDTO = Mockery::mock(CreateCastMemberInputDTO::class, [
            'Cast member name',
            self::ACTOR
        ]);

        $createCastMemberUseCase = new CreateCastMemberUseCase(
            castMemberRepository: $castMemberRepository
        );
        $response = $createCastMemberUseCase->execute(input: $createCastMemberInputDTO);

        $this->assertInstanceOf(CreateCastMemberOutputDTO::class, $response);
        $this->assertNotEmpty($response->id);
        $this->assertEquals('Cast member name', $response->name);
        $this->assertEquals(self::ACTOR, $response->type);
        $this->assertNotEmpty($response->created_at);
    }
}
