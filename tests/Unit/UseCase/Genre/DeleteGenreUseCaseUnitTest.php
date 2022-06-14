<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\DTO\Genre\GenreInputDTO;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;
use stdClass;

class DeleteGenreUseCaseUnitTest extends TestCase
{
    protected $genreId;
    protected $genreRepository;
    protected $genreInputDTO;
    protected $findGenreUseCase;

    protected function setUp(): void
    {
        $this->genreId = RamseyUuid::uuid4()->toString();
        $genreEntity = Mockery::mock(Genre::class, [
            'Genre name',
            new Uuid($this->genreId)
        ]);
        $this->genreRepository =
            Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $this->genreRepository
            ->shouldReceive('findById')
            ->once()
            ->with($this->genreId)
            ->andReturn($genreEntity);
        $this->genreInputDTO = Mockery::mock(GenreInputDTO::class, [$this->genreId]);
        $this->findGenreUseCase = new DeleteGenreUseCase($this->genreRepository);

        parent::setUp();
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function testDelete()
    {
        $this->genreRepository
            ->shouldReceive('delete')
            ->once()
            ->with($this->genreId)
            ->andReturn(true);

        $response = $this->findGenreUseCase->execute($this->genreInputDTO);

        $this->assertInstanceOf(DeleteGenreOutputDTO::class, $response);
        $this->assertTrue($response->success);
    }

    public function testDeleteFalse()
    {
        $this->genreRepository
            ->shouldReceive('delete')
            ->once()
            ->with($this->genreId)
            ->andReturn(false);

        $response = $this->findGenreUseCase->execute($this->genreInputDTO);

        $this->assertInstanceOf(DeleteGenreOutputDTO::class, $response);
        $this->assertFalse($response->success);
    }
}
