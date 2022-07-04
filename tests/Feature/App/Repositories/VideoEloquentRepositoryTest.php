<?php

namespace App\Repositories;

use App\Models\Video as VideoModel;
use App\Repositories\Eloquent\VideoEloquentRepository;
use Core\Domain\Entity\Video as VideoEntity;
use Core\Domain\Enum\Rating;
use Core\Domain\Repository\VideoRepositoryInterface;
use Tests\TestCase;

class VideoEloquentRepositoryTest extends TestCase
{
    protected VideoRepositoryInterface $videoRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->videoRepository = new VideoEloquentRepository(new VideoModel());
    }

    /** @test */
    public function should_be_implements_interface()
    {
        $this->assertInstanceOf(
            VideoRepositoryInterface::class,
            $this->videoRepository
        );
    }

    /** @test */
    public function should_be_able_to_create_a_new_video()
    {
        $videoEntity = new VideoEntity(
            title: 'Video title',
            description: 'Video description',
            yearLaunched: 2025,
            duration: 190,
            opened: true,
            rating: Rating::Rate10,
        );

        $this->videoRepository->insert($videoEntity);

        $this->assertDatabaseHas('videos', ['id' => $videoEntity->id()]);
    }
}
