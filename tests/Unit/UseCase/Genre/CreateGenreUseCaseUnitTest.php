<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Entity\Genre;
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

        Mockery::close();
    }
}
