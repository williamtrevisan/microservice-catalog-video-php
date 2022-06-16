<?php

namespace UseCase\CastMember;

use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\CastMember\DeleteCastMemberUseCase;
use Core\UseCase\DTO\CastMember\CastMemberInputDTO;
use Core\UseCase\DTO\CastMember\Delete\DeleteCastMemberOutputDTO;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;
use stdClass;

class DeleteCastMemberUseCaseUnitTest extends TestCase
{
    protected string $castMemberId;
    protected CastMemberRepositoryInterface $castMemberRepository;
    protected CastMemberInputDTO $castMemberInputDTO;
    protected DeleteCastMemberUseCase $deleteCastMemberUseCase;

    protected function setUp(): void
    {
        $this->castMemberId = RamseyUuid::uuid4()->toString();
        $this->castMemberRepository =
            Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $this->castMemberInputDTO = Mockery::mock(CastMemberInputDTO::class, [$this->castMemberId]);
        $this->deleteCastMemberUseCase = new DeleteCastMemberUseCase($this->castMemberRepository);

        parent::setUp();
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function testShouldBeReturnFalseIfCantDeleteACastMember()
    {
        $this->castMemberRepository
            ->shouldReceive('delete')
            ->once()
            ->with($this->castMemberId)
            ->andReturn(false);

        $response = $this->deleteCastMemberUseCase->execute($this->castMemberInputDTO);

        $this->assertInstanceOf(DeleteCastMemberOutputDTO::class, $response);
        $this->assertFalse($response->success);
    }

    public function testShouldBeAbleToDeleteACastMember()
    {
        $this->castMemberRepository
            ->shouldReceive('delete')
            ->once()
            ->with($this->castMemberId)
            ->andReturn(true);

        $response = $this->deleteCastMemberUseCase->execute($this->castMemberInputDTO);

        $this->assertInstanceOf(DeleteCastMemberOutputDTO::class, $response);
        $this->assertTrue($response->success);
    }
}
