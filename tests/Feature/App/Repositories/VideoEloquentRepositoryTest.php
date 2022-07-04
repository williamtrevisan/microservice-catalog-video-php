<?php

namespace App\Repositories;

use App\Models\CastMember as CastMemberModel;
use App\Models\Category as CategoryModel;
use App\Models\Genre as GenreModel;
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

    /** @test */
    public function should_be_able_to_create_a_new_video_with_relationships()
    {
        $castMembers = CastMemberModel::factory(4)->create();
        $categories = CategoryModel::factory(4)->create();
        $genres = GenreModel::factory(4)->create();

        $videoEntity = new VideoEntity(
            title: 'Video title',
            description: 'Video description',
            yearLaunched: 2025,
            duration: 190,
            opened: true,
            rating: Rating::Rate10,
        );
        foreach ($castMembers as $castMember) {
            $videoEntity->addCastMember($castMember->id);
        }
        foreach ($categories as $category) {
            $videoEntity->addCategory($category->id);
        }
        foreach ($genres as $genre) {
            $videoEntity->addGenre($genre->id);
        }

        $this->videoRepository->insert($videoEntity);

        $this->assertDatabaseHas('videos', ['id' => $videoEntity->id()]);
        $this->assertDatabaseCount('cast_member_video', 4);
        $this->assertDatabaseCount('category_video', 4);
        $this->assertDatabaseCount('genre_video', 4);
    }
}
