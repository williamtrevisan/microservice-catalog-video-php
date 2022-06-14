<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\DTO\Genre\Update\UpdateGenreInputDTO;
use Core\UseCase\DTO\Genre\Update\UpdateGenreOutputDTO;
use Core\UseCase\Genre\UpdateGenreUseCase;
use Core\UseCase\Interface\TransactionInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;
use stdClass;

class UpdateGenreUseCaseUnitTest extends TestCase
{
    protected $genreId;
    protected $genreEntity;
    protected $genreRepository;
    protected $transaction;

    protected function setUp(): void
    {
        $this->genreId = RamseyUuid::uuid4()->toString();
        $this->genreEntity = Mockery::mock(Genre::class, [
            'Genre name',
            new Uuid($this->genreId)
        ]);
        $this->genreEntity->shouldReceive('id')->andReturn($this->genreId);
        $this->genreEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));
        $this->genreEntity->shouldReceive('update', 'addCategory');
        $this->genreRepository =
            Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $this->genreRepository
            ->shouldReceive('findById', 'update')
            ->andReturn($this->genreEntity);
        $this->transaction =
            Mockery::mock(stdClass::class, TransactionInterface::class);
        $this->transaction->shouldReceive('commit');
        $this->transaction->shouldReceive('rollback');

        parent::setUp();
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function testUpdate()
    {
        $categoryId = RamseyUuid::uuid4()->toString();
        $categoryRepository =
            Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $categoryRepository
            ->shouldReceive('getIdsByListId')
            ->once()
            ->andReturn([$categoryId]);
        $updateGenreInputDTO = Mockery::mock(UpdateGenreInputDTO::class, [
            $this->genreId,
            'Genre name updated',
            [$categoryId]
        ]);

        $updateGenreUseCase = new UpdateGenreUseCase(
            $categoryRepository,
            $this->genreRepository,
            $this->transaction
        );
        $response = $updateGenreUseCase->execute($updateGenreInputDTO);

        $this->assertInstanceOf(UpdateGenreOutputDTO::class, $response);
        $this->genreEntity->shouldHaveReceived('id');
        $this->genreEntity->shouldHaveReceived('createdAt');
        $this->genreEntity->shouldHaveReceived('update');
        $this->genreEntity->shouldHaveReceived('addCategory');
        $this->genreRepository->shouldHaveReceived('update');
        $this->transaction->shouldHaveReceived('commit');
    }

    public function testShouldNotHaveReceivedAddCategoryIfCategoriesIdIsEmpty()
    {
        $categoryId = RamseyUuid::uuid4()->toString();
        $categoryRepository =
            Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $categoryRepository
            ->shouldReceive('getIdsByListId')
            ->once()
            ->andReturn([$categoryId]);
        $updateGenreInputDTO = Mockery::mock(UpdateGenreInputDTO::class, [
            $this->genreId,
            'Genre name updated',
        ]);

        $updateGenreUseCase = new UpdateGenreUseCase(
            $categoryRepository,
            $this->genreRepository,
            $this->transaction
        );
        $response = $updateGenreUseCase->execute($updateGenreInputDTO);

        $this->assertInstanceOf(UpdateGenreOutputDTO::class, $response);
        $this->genreEntity->shouldNotHaveReceived('addCategory');
    }

    public function testShouldThrowAnErrorWithNonexistentCategoryId()
    {
        $categoryRepository =
            Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $categoryRepository
            ->shouldReceive('getIdsByListId')
            ->once()
            ->andReturn([]);
        $categoryId = RamseyUuid::uuid4()->toString();
        $updateGenreInputDTO = Mockery::mock(UpdateGenreInputDTO::class, [
            $this->genreId,
            'Genre name updated',
            [$categoryId]
        ]);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            "Category with id: $categoryId, not found in database"
        );

        $updateGenreUseCase = new UpdateGenreUseCase(
            $categoryRepository,
            $this->genreRepository,
            $this->transaction
        );
        $updateGenreUseCase->execute($updateGenreInputDTO);

        $this->transaction->shouldHaveReceived('rollback');
    }

    public function testShouldThrowAnErrorWithNonexistentCategoriesId()
    {
        $categoryRepository =
            Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $categoryRepository
            ->shouldReceive('getIdsByListId')
            ->once()
            ->andReturn([]);
        $categoryId1 = RamseyUuid::uuid4()->toString();
        $categoryId2 = RamseyUuid::uuid4()->toString();
        $updateGenreInputDTO = Mockery::mock(UpdateGenreInputDTO::class, [
            $this->genreId,
            'Genre name updated',
            [$categoryId1, $categoryId2]
        ]);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            "Categories with id: $categoryId1, $categoryId2, not found in database"
        );

        $updateGenreUseCase = new UpdateGenreUseCase(
            $categoryRepository,
            $this->genreRepository,
            $this->transaction
        );
        $updateGenreUseCase->execute($updateGenreInputDTO);

        $this->transaction->shouldHaveReceived('rollback');
    }
}
