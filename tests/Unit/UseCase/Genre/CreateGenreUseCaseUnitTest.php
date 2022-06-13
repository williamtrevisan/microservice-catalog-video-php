<?php

namespace Tests\Unit\UseCase\Genre;

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
        $genreId = RamseyUuid::uuid4()->toString();
        $genreEntity = Mockery::mock(Genre::class, ['Genre name', new Uuid($genreId)]);
        $genreEntity->shouldReceive('id')->once()->andReturn($genreId);
        $genreEntity
            ->shouldReceive('createdAt')
            ->once()
            ->andReturn(date('Y-m-d H:i:s'));
        $genreRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $genreRepository->shouldReceive('insert')->once()->andReturn($genreEntity);
        $transaction = Mockery::mock(stdClass::class, TransactionInterface::class);
        $transaction->shouldReceive('commit')->once();
        $createGenreInputDTO = Mockery::mock(CreateGenreInputDTO::class, [
            'Genre name',
            [$categoryId]
        ]);

        $createGenreUseCase =
            new CreateGenreUseCase($categoryRepository, $genreRepository, $transaction);
        $response = $createGenreUseCase->execute($createGenreInputDTO);

        $this->assertInstanceOf(CreateGenreOutputDTO::class, $response);
    }

    public function testShouldThrowAnErrorWithNonexistentCategoryId()
    {
        $categoryId = RamseyUuid::uuid4()->toString();
        $categoryRepository =
            Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $categoryRepository
            ->shouldReceive('getIdsByListId')
            ->once()
            ->andReturn([]);
        $genreRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $transaction = Mockery::mock(stdClass::class, TransactionInterface::class);
        $transaction->shouldReceive('rollback')->once();
        $createGenreInputDTO = Mockery::mock(CreateGenreInputDTO::class, [
            'Genre name',
            [$categoryId]
        ]);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            "Category with id: $categoryId, not found in database"
        );

        $createGenreUseCase =
            new CreateGenreUseCase($categoryRepository, $genreRepository, $transaction);
        $createGenreUseCase->execute($createGenreInputDTO);
    }

    public function testShouldThrowAnErrorWithNonexistentCategoriesId()
    {
        $categoryId1 = RamseyUuid::uuid4()->toString();
        $categoryId2 = RamseyUuid::uuid4()->toString();
        $categoryRepository =
            Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $categoryRepository
            ->shouldReceive('getIdsByListId')
            ->once()
            ->andReturn([]);
        $genreRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $transaction = Mockery::mock(stdClass::class, TransactionInterface::class);
        $transaction->shouldReceive('rollback')->once();
        $createGenreInputDTO = Mockery::mock(CreateGenreInputDTO::class, [
            'Genre name',
            [$categoryId1, $categoryId2]
        ]);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage(
            "Categories with id: $categoryId1, $categoryId2, not found in database"
        );

        $createGenreUseCase =
            new CreateGenreUseCase($categoryRepository, $genreRepository, $transaction);
        $createGenreUseCase->execute($createGenreInputDTO);
    }
}
