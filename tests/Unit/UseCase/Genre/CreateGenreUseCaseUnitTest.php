<?php

namespace UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Exception\NotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\DTO\Genre\Create\CreateGenreInputDTO;
use Core\UseCase\DTO\Genre\Create\CreateGenreOutputDTO;
use Core\UseCase\Genre\CreateGenreUseCase;
use Core\UseCase\Interface\TransactionInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;
use stdClass;

class CreateGenreUseCaseUnitTest extends TestCase
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
        $this->genreRepository =
            Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $this->genreRepository->shouldReceive('insert')->andReturn($this->genreEntity);
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

    public function testCreate()
    {
        $categoryId = RamseyUuid::uuid4()->toString();
        $categoryRepository =
            Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $categoryRepository
            ->shouldReceive('getIdsByListId')
            ->once()
            ->andReturn([$categoryId]);
        $createGenreInputDTO = Mockery::mock(CreateGenreInputDTO::class, [
            'Genre name',
            [$categoryId]
        ]);

        $createGenreUseCase = new CreateGenreUseCase(
            $categoryRepository,
            $this->genreRepository,
            $this->transaction
        );
        $response = $createGenreUseCase->execute($createGenreInputDTO);

        $this->assertInstanceOf(CreateGenreOutputDTO::class, $response);
        $this->genreEntity->shouldHaveReceived('id');
        $this->genreEntity->shouldHaveReceived('createdAt');
        $this->genreRepository->shouldHaveReceived('insert');
        $this->transaction->shouldHaveReceived('commit');
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
        $createGenreInputDTO = Mockery::mock(CreateGenreInputDTO::class, [
            'Genre name',
            [$categoryId]
        ]);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            "Category with id: $categoryId, not found in database"
        );

        $createGenreUseCase = new CreateGenreUseCase(
            $categoryRepository,
            $this->genreRepository,
            $this->transaction
        );
        $createGenreUseCase->execute($createGenreInputDTO);

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
        $createGenreInputDTO = Mockery::mock(CreateGenreInputDTO::class, [
            'Genre name',
            [$categoryId1, $categoryId2]
        ]);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            "Categories with id: $categoryId1, $categoryId2, not found in database"
        );

        $createGenreUseCase = new CreateGenreUseCase(
            $categoryRepository,
            $this->genreRepository,
            $this->transaction
        );
        $createGenreUseCase->execute($createGenreInputDTO);

        $this->transaction->shouldHaveReceived('rollback');
    }
}
