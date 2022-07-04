<?php

namespace App\Repositories;

use App\Models\CastMember as CastMemberModel;
use App\Models\Category as CategoryModel;
use App\Models\Genre as GenreModel;
use App\Models\Video as VideoModel;
use App\Repositories\Eloquent\VideoEloquentRepository;
use Core\Domain\Entity\Video as VideoEntity;
use Core\Domain\Enum\Rating;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use DateTime;
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

        $expectedVideo = new VideoEntity(
            title: 'Video title',
            description: 'Video description',
            yearLaunched: 2025,
            duration: 190,
            opened: true,
            rating: Rating::Rate10,
        );
        foreach ($castMembers as $castMember) {
            $expectedVideo->addCastMember($castMember->id);
        }
        foreach ($categories as $category) {
            $expectedVideo->addCategory($category->id);
        }
        foreach ($genres as $genre) {
            $expectedVideo->addGenre($genre->id);
        }

        $actualVideo = $this->videoRepository->insert($expectedVideo);

        $this->assertDatabaseHas('videos', ['id' => $expectedVideo->id()]);
        $this->assertDatabaseCount('cast_member_video', 4);
        $this->assertDatabaseCount('category_video', 4);
        $this->assertDatabaseCount('genre_video', 4);
        $this->assertCount(4, $actualVideo->castMembersId);
        $this->assertEquals(
            $castMembers->pluck('id')->toArray(), $actualVideo->castMembersId
        );
        $this->assertCount(4, $actualVideo->categoriesId);
        $this->assertEquals(
            $categories->pluck('id')->toArray(), $actualVideo->categoriesId
        );
        $this->assertCount(4, $actualVideo->genresId);
        $this->assertEquals(
            $genres->pluck('id')->toArray(), $actualVideo->genresId
        );
    }

    /** @test */
    public function should_be_throw_an_expection_if_cannot_find_video()
    {
        $this->expectException(NotFoundException::class);

        $this->videoRepository->findById('videoId');
    }

    /** @test */
    public function should_be_able_to_find_a_video()
    {
        $expectedVideo = VideoModel::factory()->create();

        $actualVideo = $this->videoRepository->findById($expectedVideo->id);

        $this->assertEquals($expectedVideo->id, $actualVideo->id());
        $this->assertEquals($expectedVideo->title, $actualVideo->title);
    }

    /** @test */
    public function should_be_able_to_find_all_videos()
    {
        VideoModel::factory(10)->create();

        $actualVideos = $this->videoRepository->findAll();

        $this->assertCount(10, $actualVideos);
    }

    /** @test */
    public function should_be_able_to_find_videos_by_title_filter()
    {
        VideoModel::factory(10)->create();
        VideoModel::factory()->create(['title' => 'Video title']);

        $actualVideos = $this->videoRepository->findAll(filter: 'Video title');

        $this->assertCount(1, $actualVideos);
        $this->assertDatabaseCount('videos', 11);
    }

    /**
     * @dataProvider dataProviderPagination
     * @test
     */
    public function should_be_able_to_get_paginate_videos(
        int $page,
        int $totalPerPage,
        int $total = 50,
    ) {
        VideoModel::factory($total)->create();

        $actualVideos = $this->videoRepository->paginate(
            page: $page, totalPage: $totalPerPage
        );

        $this->assertCount($totalPerPage, $actualVideos->items());
        $this->assertEquals($total, $actualVideos->total());
        $this->assertEquals($page, $actualVideos->currentPage());
        $this->assertEquals($totalPerPage, $actualVideos->perPage());
    }

    public function dataProviderPagination(): array
    {
        return [
            ['page' => 1, 'totalPage' => 10, 'total' => 100],
            ['page' => 2, 'totalPage' => 15],
            ['page' => 3, 'totalPage' => 15],
        ];
    }

    /** @test */
    public function should_be_able_to_update_a_video()
    {
        $castMembers = CastMemberModel::factory(10)->create();
        $categories = CategoryModel::factory(10)->create();
        $genres = GenreModel::factory(10)->create();
        $expectedVideo = VideoModel::factory()->create();

        $expectedVideo = new VideoEntity(
            title: 'Video title updated',
            description: 'Video description',
            yearLaunched: 2025,
            duration: 190,
            opened: true,
            rating: Rating::Rate10,
            id: new Uuid($expectedVideo->id),
            createdAt: new DateTime($expectedVideo->created_at),
        );
        $castMembers->map(
            fn($castMember) => $expectedVideo->addCastMember($castMember->id)
        );
        $categories->map(fn($category) => $expectedVideo->addCategory($category->id));
        $genres->map(fn($genre) => $expectedVideo->addGenre($genre->id));

        $actualVideo = $this->videoRepository->update($expectedVideo);

        $this->assertDatabaseHas('videos', ['title' => 'Video title updated']);
        $this->assertDatabaseCount('cast_member_video', 10);
        $this->assertDatabaseCount('category_video', 10);
        $this->assertDatabaseCount('genre_video', 10);
        $this->assertEquals(
            $castMembers->pluck('id')->toArray(),
            $actualVideo->castMembersId
        );
        $this->assertEquals(
            $categories->pluck('id')->toArray(),
            $actualVideo->categoriesId
        );
        $this->assertEquals(
            $genres->pluck('id')->toArray(),
            $actualVideo->genresId
        );
    }

    /** @test */
    public function should_be_throw_an_exception_if_cannot_find_video_for_delete()
    {
        $this->expectException(NotFoundException::class);

        $this->videoRepository->delete('videoId');
    }

    /** @test */
    public function should_be_able_to_delete_a_video()
    {
        $expectedVideo = VideoModel::factory()->create();

        $hasDeleted = $this->videoRepository->delete($expectedVideo->id);

        $this->assertSoftDeleted('videos', ['id' => $expectedVideo->id]);
        $this->assertTrue($hasDeleted);
    }
}
