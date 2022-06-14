<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\DTO\Genre\Delete\DeleteGenreOutputDTO;
use Core\UseCase\DTO\Genre\GenreInputDTO;
use Core\UseCase\Genre\DeleteGenreUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;
use stdClass;

class DeleteGenreUseCaseUnitTest extends TestCase
{
    protected string $genreId;
    protected GenreRepositoryInterface $genreRepository;
    protected GenreInputDTO $genreInputDTO;
    protected DeleteGenreUseCase $deleteGenreUseCase;

    protected function setUp(): void
    {
        $this->genreId = RamseyUuid::uuid4()->toString();
        $this->genreRepository =
            Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $this->genreInputDTO = Mockery::mock(GenreInputDTO::class, [$this->genreId]);
        $this->deleteGenreUseCase = new DeleteGenreUseCase($this->genreRepository);

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

        $response = $this->deleteGenreUseCase->execute($this->genreInputDTO);

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

        $response = $this->deleteGenreUseCase->execute($this->genreInputDTO);

        $this->assertInstanceOf(DeleteGenreOutputDTO::class, $response);
        $this->assertFalse($response->success);
    }
}
