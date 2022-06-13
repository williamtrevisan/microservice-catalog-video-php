<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\Genre\CreateGenreUseCase;
use Core\UseCase\Interface\TransactionInterface;
use CreateGenreInputDTO;
use CreateGenreOutputDTO;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;
use stdClass;

class CreateGenreUseCaseUnitTest extends TestCase
{
    public function testCreate()
    {
        $categoryId = RamseyUuid::uuid4()->toString();
        $categoryRepository =
            Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $categoryRepository->shouldReceive('getIdsByListId')->once();
        $genreRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $genreRepository->shouldReceive('insert')->once();
        $transaction = Mockery::mock(stdClass::class, TransactionInterface::class);
        $transaction->shouldReceive('commit', 'rollback')->once();
        $createGenreInputDTO = Mockery::mock(CreateGenreInputDTO::class, [
            'Genre name',
            [$categoryId]
        ]);

        $createGenreUseCase =
            new CreateGenreUseCase($categoryRepository, $genreRepository, $transaction);
        $response = $createGenreUseCase->execute($createGenreInputDTO);

        $this->assertInstanceOf(CreateGenreOutputDTO::class, $response);

        Mockery::close();
    }
}
