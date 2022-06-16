<?php

namespace Core\UseCase\Genre;

use App\Models\Genre as GenreModel;
use App\Repositories\Eloquent\GenreEloquentRepository;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\DTO\Genre\GenreInputDTO;
use Core\UseCase\Genre\FindGenreUseCase;
use Tests\TestCase;

class FindGenreUseCaseTest extends TestCase
{
    protected GenreRepositoryInterface $genreRepository;
    protected FindGenreUseCase $findGenreUseCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->genreRepository = new GenreEloquentRepository(genreModel: new GenreModel());
        $this->findGenreUseCase = new FindGenreUseCase(genreRepository: $this->genreRepository);
    }

    public function testShouldBeAbleToFindAGenreById()
    {
        $genre = GenreModel::factory()->create();
        $findGenreInputDTO = new GenreInputDTO(id: $genre->id);

        $response = $this->findGenreUseCase->execute(input: $findGenreInputDTO);

        $this->assertEquals($genre->id, $response->id);
        $this->assertEquals($genre->name, $response->name);
    }
}
