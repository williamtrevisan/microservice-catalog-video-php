<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Repository\GenreRepositoryInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class FindGenreUseCaseUnitTest extends TestCase
{
    public function testFindSingle()
    {
        $id = Uuid::uuid4()->toString();
        $genreEntity = Mockery::mock(Genre::class, [
            'id' => $id,
            'name' => 'Genre name'
        ]);
        $genreRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $genreRepository->shouldReceive('findById')->andReturn($genreEntity);
        $genreInputDTO = Mockery::mock(GenreInputDTO::class, ['id' => $id]);

        $findGenreUseCase = new FindGenreUseCase($genreRepository);
        $response = $findGenreUseCase->execute($genreInputDTO);

        $this->assertInstanceOf(FindGenreOutputDTO::class, $response);
        $this->assertEquals($id, $response->id());
        $this->assertEquals('Genre name', $response->name);
        $this->assertTrue($response->isActive);
        $this->assertIsArray($response->categoriesId);
        $this->assertCount(0, $response->categoriesId);
        $this->assertNotEmpty($response->createdAt());
    }
}
