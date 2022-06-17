<?php

namespace UseCase\Video;

use Core\Domain\Entity\Video as VideoEntity;
use Core\Domain\Enum\Rating;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\DTO\Video\Create\{CreateVideoInputDTO, CreateVideoOutputDTO};
use Core\UseCase\Interface\{EventDispatcherInterface, FileStorageInterface, TransactionInterface};
use Core\UseCase\Video\CreateVideoUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;
use stdClass;

class CreateVideoUseCaseUnitTest extends TestCase
{
    private VideoEntity $videoEntity;
    private CastMemberRepositoryInterface $castMemberRepository;
    private CategoryRepositoryInterface $categoryRepository;
    private GenreRepositoryInterface $genreRepository;
    private TransactionInterface $transaction;
    private CreateVideoUseCase $createVideoUseCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->videoEntity = $this->createVideoEntityMock();
        $this->castMemberRepository = $this->createCastMemberRepositoryMock();
        $this->categoryRepository = $this->createCategoryRepositoryMock();
        $this->genreRepository = $this->createGenreRepositoryMock();
        $this->transaction = $this->createTransactionMock();
        $this->createVideoUseCase = new CreateVideoUseCase(
            castMemberRepository: $this->castMemberRepository,
            categoryRepository: $this->categoryRepository,
            genreRepository: $this->genreRepository,
            videoRepository: $this->createVideoRepositoryMock(),
            transaction: $this->transaction,
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
        return Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
    }

    private function createCategoryRepositoryMock()
    {
        return Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
    }

    private function createGenreRepositoryMock()
    {
        return Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
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

    private function createDataProvider(): array
    {
        return [
            ['Cast member', ['castMemberId']],
            ['Cast members', ['castMemberId1', 'castMemberId2']],
            [
                'Cast members',
                ['castMemberId1', 'castMemberId2', 'castMemberId3', 'castMemberId4']
            ],
            ['Category', ['categoryId']],
            ['Categories', ['categoryId1', 'categoryId2']],
            ['Categories', ['categoryId1', 'categoryId2', 'categoryId3', 'categoryId4']],
            ['Genre', ['GenreId']],
            ['Genres', ['GenreId1', 'GenreId2']],
            ['Genres', ['GenreId1', 'GenreId2', 'GenreId3', 'GenreId4']],
        ];
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /** @test */
    public function should_be_able_to_create_a_new_video()
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
        $this->castMemberRepository->shouldNotHaveReceived('getIdsByListId');
        $this->categoryRepository->shouldNotHaveReceived('getIdsByListId');
    }

    /**
     * @test
     * @dataProvider createDataProvider
     */
    public function should_throw_an_exception_if_nonexistent_id_is_received(
        string $label,
        array $listId
    ) {
        $isCastMember = in_array($label, ['Cast member', 'Cast members']);
        $isCategory = in_array($label, ['Category', 'Categories']);
        $isGenre = in_array($label, ['Genre', 'Genres']);
        $this->castMemberRepository->shouldReceive('getIdsByListId')->andReturn([]);
        $this->categoryRepository->shouldReceive('getIdsByListId')->andReturn([]);
        $this->genreRepository->shouldReceive('getIdsByListId')->andReturn([]);
        $createVideoInputDTO = new CreateVideoInputDTO(
            title: 'Video title',
            description: 'Video description',
            yearLaunched: 2001,
            duration: 190,
            opened: true,
            rating: 'L',
            castMembersId: $isCastMember ? $listId : [],
            categoriesId: $isCategory ? $listId : [],
            genresId: $isGenre ? $listId : [],
        );

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            sprintf('%s with id: %s, not found in database', $label, implode(', ', $listId))
        );

        $this->createVideoUseCase->execute($createVideoInputDTO);

        $this->transaction->shouldHaveReceived('rollback');
        if ($isCastMember) {
            $this->castMemberRepository->shouldHaveReceived('getIdsByListId');
            $this->categoryRepository->shouldNotHaveReceived('getIdsByListId');
            $this->genreRepository->shouldNotHaveReceived('getIdsByListId');
        }
        if ($isCategory) {
            $this->castMemberRepository->shouldNotHaveReceived('getIdsByListId');
            $this->categoryRepository->shouldHaveReceived('getIdsByListId');
            $this->genreRepository->shouldNotHaveReceived('getIdsByListId');
        }
        if ($isGenre) {
            $this->castMemberRepository->shouldNotHaveReceived('getIdsByListId');
            $this->categoryRepository->shouldNotHaveReceived('getIdsByListId');
            $this->genreRepository->shouldHaveReceived('getIdsByListId');
        }
    }
}
