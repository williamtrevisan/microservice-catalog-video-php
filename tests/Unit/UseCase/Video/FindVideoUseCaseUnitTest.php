<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\Entity\Video;
use Core\Domain\Enum\Rating;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\DTO\Video\Find\FindVideoOutputDTO;
use Core\UseCase\DTO\Video\VideoInputDTO;
use Core\UseCase\Video\FindVideoUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;
use stdClass;

class FindVideoUseCaseUnitTest extends TestCase
{
    protected string $videoId;
    protected VideoRepositoryInterface $videoRepository;
    protected FindVideoUseCase $findVideoUseCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->videoId = RamseyUuid::uuid4()->toString();
        $this->videoRepository = $this->createVideoRepositoryMock();
        $this->findVideoUseCase = new FindVideoUseCase($this->videoRepository);
    }

    private function createVideoEntityMock()
    {
        $videoEntity = Mockery::mock(Video::class, [
            'Video title',
            'Video description',
            2001,
            190,
            true,
            Rating::L,
            new Uuid($this->videoId)
        ]);
        $videoEntity->shouldReceive('id')->andReturn($this->videoId);
        $videoEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));
        $videoEntity
            ->shouldReceive(
                'thumbFile',
                'thumbHalfFile',
                'bannerFile',
                'trailerFile',
                'videoFile'
            )
            ->andReturn(null);

        return $videoEntity;
    }

    private function createVideoRepositoryMock()
    {
        return Mockery::mock(stdClass::class, VideoRepositoryInterface::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /** @test */
    public function should_be_able_to_find_a_video_by_id()
    {
        $this->videoRepository
            ->shouldReceive('findById')
            ->once()
            ->with($this->videoId)
            ->andReturn($this->createVideoEntityMock());
        $videoInputDTO = Mockery::mock(VideoInputDTO::class, [$this->videoId]);

        $response = $this->findVideoUseCase->execute($videoInputDTO);

        $this->assertInstanceOf(FindVideoOutputDTO::class, $response);
        $this->assertEquals($this->videoId, $response->id);
        $this->assertEquals('Video title', $response->title);
        $this->assertEquals('Video description', $response->description);
        $this->assertEquals(2001, $response->year_launched);
        $this->assertEquals(190, $response->duration);
        $this->assertTrue($response->opened);
        $this->assertEquals('L', $response->rating);
        $this->assertFalse($response->published);
        $this->assertNull($response->thumbFile);
        $this->assertNull($response->thumbHalfFile);
        $this->assertNull($response->bannerFile);
        $this->assertNull($response->trailerFile);
        $this->assertNull($response->videoFile);
        $this->assertNotEmpty($response->created_at);
    }

    /** @test */
    public function should_throw_an_exception_if_not_found_video()
    {
        $this->expectException(NotFoundException::class);

        $this->videoRepository
            ->shouldReceive('findById')
            ->once()
            ->with($this->videoId)
            ->andThrow(NotFoundException::class);
        $videoInputDTO = Mockery::mock(VideoInputDTO::class, [$this->videoId]);

        $this->findVideoUseCase->execute($videoInputDTO);
    }
}
