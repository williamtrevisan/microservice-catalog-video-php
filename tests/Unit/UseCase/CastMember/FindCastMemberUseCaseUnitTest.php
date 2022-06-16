<?php

namespace UseCase\CastMember;

use Core\Domain\Entity\CastMember as CastMemberEntity;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\CastMember\FindCastMemberUseCase;
use Core\UseCase\DTO\CastMember\CastMemberInputDTO;
use Core\UseCase\DTO\CastMember\Find\FindCastMemberOutputDTO;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;
use stdClass;

class FindCastMemberUseCaseUnitTest extends TestCase
{
    const DIRECTOR = 1;

    protected string $castMemberId;
    protected CastMemberEntity $castMemberEntity;
    protected CastMemberRepositoryInterface $castMemberRepository;
    protected FindCastMemberUseCase $findCastMemberUseCase;

    protected function setUp(): void
    {
        $this->castMemberId = RamseyUuid::uuid4()->toString();
        $this->castMemberEntity = Mockery::mock(CastMemberEntity::class, [
            'Cast member name',
            CastMemberType::Director,
            new Uuid($this->castMemberId)
        ]);
        $this->castMemberEntity->shouldReceive('id')->andReturn($this->castMemberId);
        $this->castMemberEntity
            ->shouldReceive('createdAt')
            ->andReturn(date('Y-m-d H:i:s'));
        $this->castMemberRepository = Mockery::mock(
            stdClass::class,
            CastMemberRepositoryInterface::class
        );
        $this->findCastMemberUseCase = new FindCastMemberUseCase(
            $this->castMemberRepository
        );

        parent::setUp();
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function testShouldBeAbleToFindACastMemberById()
    {
        $this->castMemberRepository
            ->shouldReceive('findById')
            ->once()
            ->with($this->castMemberId)
            ->andReturn($this->castMemberEntity);
        $castMemberInputDTO = Mockery::mock(CastMemberInputDTO::class, [
            $this->castMemberId
        ]);

        $response = $this->findCastMemberUseCase->execute($castMemberInputDTO);

        $this->castMemberEntity->shouldHaveReceived('id');
        $this->castMemberEntity->shouldHaveReceived('createdAt');
        $this->assertInstanceOf(FindCastMemberOutputDTO::class, $response);
        $this->assertEquals($this->castMemberId, $response->id);
        $this->assertEquals('Cast member name', $response->name);
        $this->assertEquals(self::DIRECTOR, $response->type);
        $this->assertNotEmpty($response->created_at);
    }

    public function testShouldBeThrowAnExceptionIfNotFoundCastMemberById()
    {
        $this->expectException(NotFoundException::class);

        $this->castMemberRepository
            ->shouldReceive('findById')
            ->once()
            ->with($this->castMemberId)
            ->andReturn([])
            ->andThrow(NotFoundException::class);
        $castMemberInputDTO = Mockery::mock(CastMemberInputDTO::class, [
            $this->castMemberId
        ]);

        $this->findCastMemberUseCase->execute($castMemberInputDTO);
    }
}
