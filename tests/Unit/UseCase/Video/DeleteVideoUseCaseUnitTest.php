<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\DTO\Video\Delete\DeleteVideoOutputDTO;
use Core\UseCase\DTO\Video\VideoInputDTO;
use Core\UseCase\Video\DeleteVideoUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;
use stdClass;

class DeleteVideoUseCaseUnitTest extends TestCase
{
    protected string $videoId;
    protected VideoRepositoryInterface $videoRepository;
    protected VideoInputDTO $videoInputDTO;
    protected DeleteVideoUseCase $deleteVideoUseCase;

    protected function setUp(): void
    {
        $this->videoId = RamseyUuid::uuid4()->toString();
        $this->videoRepository =
            Mockery::mock(stdClass::class, VideoRepositoryInterface::class);
        $this->videoInputDTO = Mockery::mock(VideoInputDTO::class, [$this->videoId]);
        $this->deleteVideoUseCase = new DeleteVideoUseCase($this->videoRepository);

        parent::setUp();
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /** @test */
    public function should_be_able_to_delete_a_video()
    {
        $this->videoRepository
            ->shouldReceive('delete')
            ->once()
            ->with($this->videoId)
            ->andReturn(true);

        $response = $this->deleteVideoUseCase->execute($this->videoInputDTO);

        $this->assertInstanceOf(DeleteVideoOutputDTO::class, $response);
        $this->assertTrue($response->success);
    }

    /** @test */
    public function should_return_false_if_cant_delete_a_video()
    {
        $this->videoRepository
            ->shouldReceive('delete')
            ->once()
            ->with($this->videoId)
            ->andReturn(false);

        $response = $this->deleteVideoUseCase->execute($this->videoInputDTO);

        $this->assertInstanceOf(DeleteVideoOutputDTO::class, $response);
        $this->assertFalse($response->success);
    }
}
