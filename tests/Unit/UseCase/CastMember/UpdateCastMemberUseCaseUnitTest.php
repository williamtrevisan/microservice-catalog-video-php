<?php

namespace UseCase\CastMember;

use Core\Domain\Entity\CastMember;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\CastMember\UpdateCastMemberUseCase;
use Core\UseCase\DTO\CastMember\Update\UpdateCastMemberInputDTO;
use Core\UseCase\DTO\CastMember\Update\UpdateCastMemberOutputDTO;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;
use stdClass;

class UpdateCastMemberUseCaseUnitTest extends TestCase
{
    protected string $castMemberId;
    protected CastMember $castMemberEntity;
    protected CastMemberRepositoryInterface $castMemberRepository;
    protected UpdateCastMemberUseCase $updateCastMemberUseCase;

    protected function setUp(): void
    {
        $this->castMemberId = RamseyUuid::uuid4()->toString();
        $this->castMemberEntity = Mockery::mock(CastMember::class, [
            'Cast member name',
            CastMemberType::Actor,
            new Uuid($this->castMemberId)
        ]);
        $this->castMemberEntity->shouldReceive('id')->andReturn($this->castMemberId);
        $this->castMemberEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));
        $this->castMemberEntity->shouldReceive('update');
        $this->castMemberRepository =
            Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $this->castMemberRepository
            ->shouldReceive('update')
            ->andReturn($this->castMemberEntity);
        $this->updateCastMemberUseCase = new UpdateCastMemberUseCase($this->castMemberRepository);

        parent::setUp();
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function testShouldThrowAnExceptionIfNotFoundACastMember()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Cast member with id: $this->castMemberId not found");

        $this->castMemberRepository
            ->shouldReceive('findById')
            ->once()
            ->with($this->castMemberId)
            ->andThrow(
                NotFoundException::class,
                "Cast member with id: $this->castMemberId not found"
            );
        $updateCastMemberInputDTO = Mockery::mock(UpdateCastMemberInputDTO::class, [
            $this->castMemberId,
            'Cast member name updated',
        ]);

        $this->updateCastMemberUseCase->execute($updateCastMemberInputDTO);
    }

    public function testShouldBeAbleToUpdateACastMember()
    {
        $this->castMemberRepository
            ->shouldReceive('findById')
            ->once()
            ->with($this->castMemberId)
            ->andReturn($this->castMemberEntity);
        $updateCastMemberInputDTO = Mockery::mock(UpdateCastMemberInputDTO::class, [
            $this->castMemberId,
            'Cast member name updated',
        ]);

        $response = $this->updateCastMemberUseCase->execute($updateCastMemberInputDTO);

        $this->assertInstanceOf(UpdateCastMemberOutputDTO::class, $response);
        $this->castMemberEntity->shouldHaveReceived('id');
        $this->castMemberEntity->shouldHaveReceived('createdAt');
        $this->castMemberEntity->shouldHaveReceived('update');
        $this->castMemberRepository->shouldHaveReceived('update');
    }
}
