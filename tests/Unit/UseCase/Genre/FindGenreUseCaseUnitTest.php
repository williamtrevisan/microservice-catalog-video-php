<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\DTO\Genre\Find\FindGenreOutputDTO;
use Core\UseCase\DTO\Genre\GenreInputDTO;
use Core\UseCase\Genre\FindGenreUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;
use stdClass;

class FindGenreUseCaseUnitTest extends TestCase
{
    public function testFindSingle()
    {
        $id = RamseyUuid::uuid4()->toString();
        $genreEntity = Mockery::mock(Genre::class, ['Genre name', new Uuid($id)]);
        $genreEntity->shouldReceive('id')->andReturn($id);
        $genreEntity->shouldReceive('createdAt')->andReturn(date('Y-m-d H:i:s'));
        $genreRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $genreRepository->shouldReceive('findById')->andReturn($genreEntity);
        $genreInputDTO = Mockery::mock(GenreInputDTO::class, [$id]);

        $findGenreUseCase = new FindGenreUseCase($genreRepository);
        $response = $findGenreUseCase->execute($genreInputDTO);

        $this->assertInstanceOf(FindGenreOutputDTO::class, $response);
        $this->assertEquals($id, $response->id);
        $this->assertEquals('Genre name', $response->name);
        $this->assertTrue($response->is_active);
        $this->assertNotEmpty($response->created_at);
    }
}
