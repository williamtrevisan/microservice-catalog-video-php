<?php

namespace UseCase\Video;

use Core\Domain\Entity\Video as VideoEntity;
use Core\Domain\Enum\Rating;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\DTO\Video\Create\{CreateVideoInputDTO, CreateVideoOutputDTO};
use Core\UseCase\Interface\{EventDispatcherInterface, FileStorageInterface, TransactionInterface};
use Core\UseCase\Video\CreateVideoUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class CreateVideoUseCaseUnitTest extends TestCase
{
    private VideoEntity $videoEntity;
    private CreateVideoUseCase $createVideoUseCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->videoEntity = $this->createVideoEntityMock();
        $this->createVideoUseCase = new CreateVideoUseCase(
            castMemberRepository: $this->createCastMemberRepositoryMock(),
            categoryRepository: $this->createCategoryRepositoryMock(),
            genreRepository: $this->createGenreRepositoryMock(),
            videoRepository: $this->createVideoRepositoryMock(),
            transaction: $this->createTransactionMock(),
            fileStorage: $this->createFileStorageMock(),
            eventDispatcher: $this->createEventDispatcherMock(),
        );
    }

    private function createVideoEntityMock()
    {
        $videoEntity = Mockery::mock(VideoEntity::class, [
            'Video title',
            'Video description',
            2001,
            190,
            true,
            Rating::L,
        ]);
        $videoEntity->shouldReceive('id');
        $videoEntity->shouldReceive('createdAt');
        $videoEntity->shouldReceive('addCastMember', 'addCategory', 'addGenre');

        return $videoEntity;
    }

    private function createCastMemberRepositoryMock()
    {
        $videoRepository = Mockery::mock(
            stdClass::class,
            CastMemberRepositoryInterface::class
        );
        $videoRepository->shouldReceive('getIdsByListId')->andReturn([]);

        return $videoRepository;
    }

    private function createCategoryRepositoryMock()
    {
        $videoRepository = Mockery::mock(
            stdClass::class,
            CategoryRepositoryInterface::class
        );
        $videoRepository->shouldReceive('getIdsByListId')->andReturn([]);

        return $videoRepository;
    }

    private function createGenreRepositoryMock()
    {
        $videoRepository = Mockery::mock(
            stdClass::class,
            GenreRepositoryInterface::class
        );
        $videoRepository->shouldReceive('getIdsByListId')->andReturn([]);

        return $videoRepository;
    }

    private function createVideoRepositoryMock()
    {
        $videoRepository = Mockery::mock(
            stdClass::class,
            VideoRepositoryInterface::class
        );
        $videoRepository->shouldReceive('insert')->andReturn($this->videoEntity);
        $videoRepository->shouldReceive('updateMedia');

        return $videoRepository;
    }

    private function createTransactionMock()
    {
        $transaction = Mockery::mock(stdClass::class, TransactionInterface::class);
        $transaction->shouldReceive('commit', 'rollback');

        return $transaction;
    }

    private function createFileStorageMock()
    {
        $fileStorage = Mockery::mock(stdClass::class, FileStorageInterface::class);
        $fileStorage->shouldReceive('store')->andReturn('fakepath/video-file.mp4');

        return $fileStorage;
    }

    private function createEventDispatcherMock()
    {
        $eventDispatcher = Mockery::mock(
            stdClass::class,
            EventDispatcherInterface::class
        );
        $eventDispatcher->shouldReceive('dispatch');

        return $eventDispatcher;
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /** @test */
    public function it_should_be_able_to_create_a_new_video()
    {
        $createVideoInputDTO = new CreateVideoInputDTO(
            title: 'Video title',
            description: 'Video description',
            yearLaunched: 2001,
            duration: 190,
            opened: true,
            rating: 'L',
            castMembersId: [],
            categoriesId: [],
            genresId: [],
        );

        $response = $this->createVideoUseCase->execute(input: $createVideoInputDTO);

        $this->assertInstanceOf(CreateVideoOutputDTO::class, $response);
    }
}
