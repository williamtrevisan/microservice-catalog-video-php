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
use stdClass;

class CreateVideoUseCaseUnitTest extends TestCase
{
    private VideoEntity $videoEntity;
    private CastMemberRepositoryInterface $castMemberRepository;
    private CategoryRepositoryInterface $categoryRepository;
    private GenreRepositoryInterface $genreRepository;
    private VideoRepositoryInterface $videoRepository;
    private TransactionInterface $transaction;
    private FileStorageInterface $fileStorage;
    private EventDispatcherInterface $eventDispatcher;
    private CreateVideoUseCase $createVideoUseCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->videoEntity = $this->createVideoEntityMock();
        $this->castMemberRepository = $this->createCastMemberRepositoryMock();
        $this->categoryRepository = $this->createCategoryRepositoryMock();
        $this->genreRepository = $this->createGenreRepositoryMock();
        $this->videoRepository = $this->createVideoRepositoryMock();
        $this->transaction = $this->createTransactionMock();
        $this->fileStorage = $this->createFileStorageMock();
        $this->eventDispatcher = $this->createEventDispatcherMock();
        $this->createVideoUseCase = new CreateVideoUseCase(
            castMemberRepository: $this->castMemberRepository,
            categoryRepository: $this->categoryRepository,
            genreRepository: $this->genreRepository,
            videoRepository: $this->videoRepository,
            transaction: $this->transaction,
            fileStorage: $this->fileStorage,
            eventDispatcher: $this->eventDispatcher,
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
        $fileStorage->shouldReceive('store')->andReturn('tmp/video-file.mp4');

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

    private function createDataProviderEntitiesId(): array
    {
        return [
            'one nonexistent cast member id' => ['Cast member', ['castMemberId']],
            'two nonexistent cast members id' => [
                'Cast members',
                ['castMemberId1', 'castMemberId2']
            ],
            'four nonexistent cast members id' => [
                'Cast members',
                ['castMemberId1', 'castMemberId2', 'castMemberId3', 'castMemberId4']
            ],
            'one nonexistent category id' => ['Category', ['categoryId']],
            'two nonexistent categories id' => ['Categories', ['categoryId1', 'categoryId2']],
            'four nonexistent categories id' => [
                'Categories',
                ['categoryId1', 'categoryId2', 'categoryId3', 'categoryId4']
            ],
            'one nonexistent genre id' => ['Genre', ['GenreId']],
            'two nonexistent genres id' => ['Genres', ['GenreId1', 'GenreId2']],
            'four nonexistent genres id' => [
                'Genres',
                ['GenreId1', 'GenreId2', 'GenreId3', 'GenreId4']
            ],
        ];
    }

    private function createDataProviderFiles(): array
    {
        return [
            'all files empty' => [
                'thumbFile' => ['file' => [], 'expected' => null],
                'thumbHalfFile' => ['file' => [], 'expected' => null],
                'bannerFile' => ['file' => [], 'expected' => null],
                'trailerFile' => ['file' => [], 'expected' => null],
                'videoFile' => ['file' => [], 'expected' => null],
                'allFilesEmpty' => true,
            ],
            'four files empty' => [
                'thumbFile' => ['file' => [], 'expected' => null],
                'thumbHalfFile' => ['file' => [], 'expected' => null],
                'bannerFile' => ['file' => [], 'expected' => null],
                'trailerFile' => ['file' => [], 'expected' => null],
                'videoFile' => [
                    'file' => ['tmp' => 'tmp/video-file.mp4'],
                    'expected' => 'tmp/video-file.mp4',
                ],
                'allFilesEmpty' => false,
            ],
            'three files empty' => [
                'thumbFile' => ['file' => [], 'expected' => null],
                'thumbHalfFile' => [
                    'file' => ['tmp' => 'tmp/thumb-half-file.mp4'],
                    'expected' => 'tmp/video-file.mp4',
                ],
                'bannerFile' => ['file' => [], 'expected' => null],
                'trailerFile' => ['file' => [], 'expected' => null],
                'videoFile' => [
                    'file' => ['tmp' => 'tmp/video-file.mp4'],
                    'expected' => 'tmp/video-file.mp4',
                ],
                'allFilesEmpty' => false,
            ],
            'two files empty' => [
                'thumbFile' => ['file' => [], 'expected' => null],
                'thumbHalfFile' => [
                    'file' => ['tmp' => 'tmp/thumb-half-file.mp4'],
                    'expected' => 'tmp/video-file.mp4',
                ],
                'bannerFile' => [
                    'file' => ['tmp' => 'tmp/banner-file.mp4'],
                    'expected' => 'tmp/video-file.mp4',
                ],
                'trailerFile' => ['file' => [], 'expected' => null],
                'videoFile' => [
                    'file' => ['tmp' => 'tmp/video-file.mp4'],
                    'expected' => 'tmp/video-file.mp4',
                ],
                'allFilesEmpty' => false,
            ],
            'one file empty' => [
                'thumbFile' => [
                    'file' => ['tmp' => 'tmp/thumb-file.mp4'],
                    'expected' => 'tmp/video-file.mp4',
                ],
                'thumbHalfFile' => [
                    'file' => ['tmp' => 'tmp/thumb-half-file.mp4'],
                    'expected' => 'tmp/video-file.mp4',
                ],
                'bannerFile' => [
                    'file' => ['tmp' => 'tmp/banner-file.mp4'],
                    'expected' => 'tmp/video-file.mp4',
                ],
                'trailerFile' => ['file' => [], 'expected' => null],
                'videoFile' => [
                    'file' => ['tmp' => 'tmp/video-file.mp4'],
                    'expected' => 'tmp/video-file.mp4',
                ],
                'allFilesEmpty' => false,
            ],
            'empty even files' => [
                'thumbFile' => [
                    'file' => ['tmp' => 'tmp/thumb-file.mp4'],
                    'expected' => 'tmp/video-file.mp4',
                ],
                'thumbHalfFile' => ['file' => [], 'expected' => null],
                'bannerFile' => [
                    'file' => ['tmp' => 'tmp/banner-file.mp4'],
                    'expected' => 'tmp/video-file.mp4',
                ],
                'trailerFile' => ['file' => [], 'expected' => null],
                'videoFile' => [
                    'file' => ['tmp' => 'tmp/video-file.mp4'],
                    'expected' => 'tmp/video-file.mp4',
                ],
                'allFilesEmpty' => false,
            ],
            'empty odd files' => [
                'thumbFile' => ['file' => [], 'expected' => null],
                'thumbHalfFile' => [
                    'file' => ['tmp' => 'tmp/thumb-half-file.mp4'],
                    'expected' => 'tmp/video-file.mp4',
                ],
                'bannerFile' => ['file' => [], 'expected' => null],
                'trailerFile' => [
                    'file' => ['tmp' => 'tmp/trailer-file.mp4'],
                    'expected' => 'tmp/video-file.mp4',
                ],
                'videoFile' => ['file' => [], 'expected' => null],
                'allFilesEmpty' => false,
            ],
            'all files with path' => [
                'thumbFile' => [
                    'file' => ['tmp' => 'tmp/thumb-file.mp4'],
                    'expected' => 'tmp/video-file.mp4',
                ],
                'thumbHalfFile' => [
                    'file' => ['tmp' => 'tmp/thumb-half-file.mp4'],
                    'expected' => 'tmp/video-file.mp4',
                ],
                'bannerFile' => [
                    'file' => ['tmp' => 'tmp/banner-file.mp4'],
                    'expected' => 'tmp/video-file.mp4',
                ],
                'trailerFile' => [
                    'file' => ['tmp' => 'tmp/trailer-file.mp4'],
                    'expected' => 'tmp/video-file.mp4',
                ],
                'videoFile' => [
                    'file' => ['tmp' => 'tmp/video-file.mp4'],
                    'expected' => 'tmp/video-file.mp4',
                ],
                'allFilesEmpty' => false,
            ],
        ];
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /**
     * @test
     * @dataProvider createDataProviderEntitiesId
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
            $this->videoEntity->shouldHaveReceived('addCastMember');
            $this->castMemberRepository->shouldHaveReceived('getIdsByListId');
            $this->categoryRepository->shouldNotHaveReceived('getIdsByListId');
            $this->genreRepository->shouldNotHaveReceived('getIdsByListId');
        }
        if ($isCategory) {
            $this->videoEntity->shouldHaveReceived('addCategory');
            $this->castMemberRepository->shouldNotHaveReceived('getIdsByListId');
            $this->categoryRepository->shouldHaveReceived('getIdsByListId');
            $this->genreRepository->shouldNotHaveReceived('getIdsByListId');
        }
        if ($isGenre) {
            $this->videoEntity->shouldHaveReceived('addGenre');
            $this->castMemberRepository->shouldNotHaveReceived('getIdsByListId');
            $this->categoryRepository->shouldNotHaveReceived('getIdsByListId');
            $this->genreRepository->shouldHaveReceived('getIdsByListId');
        }
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
        $this->genreRepository->shouldNotHaveReceived('getIdsByListId');
        $this->videoEntity->shouldNotHaveReceived('addCastMember');
        $this->videoEntity->shouldNotHaveReceived('addCategory');
        $this->videoEntity->shouldNotHaveReceived('addGenre');
        $this->videoRepository->shouldHaveReceived('insert');
        $this->fileStorage->shouldNotHaveReceived('store');
        $this->eventDispatcher->shouldNotHaveReceived('dispatch');
        $this->videoRepository->shouldHaveReceived('updateMedia');
        $this->transaction->shouldHaveReceived('commit');
    }

    /**
     * @test
     * @dataProvider createDataProviderFiles
     */
    public function should_be_return_a_files_path_expected(
        array $thumbFile,
        array $thumbHalfFile,
        array $bannerFile,
        array $trailerFile,
        array $videoFile,
        bool $allFilesEmpty
    ) {
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
            thumbFile: $thumbFile['file'],
            thumbHalfFile: $thumbHalfFile['file'],
            bannerFile: $bannerFile['file'],
            trailerFile: $trailerFile['file'],
            videoFile: $videoFile['file'],
        );

        $response = $this->createVideoUseCase->execute(input: $createVideoInputDTO);

        if (! $allFilesEmpty) $this->fileStorage->shouldHaveReceived('store');
        if ($videoFile['file']) $this->eventDispatcher->shouldHaveReceived('dispatch');
        $this->assertEquals($thumbFile['expected'], $response->thumbFile);
        $this->assertEquals($thumbHalfFile['expected'], $response->thumbHalfFile);
        $this->assertEquals($bannerFile['expected'], $response->bannerFile);
        $this->assertEquals($trailerFile['expected'], $response->trailerFile);
        $this->assertEquals($videoFile['expected'], $response->videoFile);
    }
}
