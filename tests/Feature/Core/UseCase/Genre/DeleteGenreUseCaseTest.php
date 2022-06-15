<?php

namespace Tests\Feature\Core\UseCase\Genre;

use App\Models\Genre as GenreModel;
use App\Repositories\Eloquent\GenreEloquentRepository;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\DTO\Genre\GenreInputDTO;
use Core\UseCase\Genre\DeleteGenreUseCase;
use Tests\TestCase;

class DeleteGenreUseCaseTest extends TestCase
{
    protected GenreRepositoryInterface $genreRepository;
    protected DeleteGenreUseCase $deleteGenreUseCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->genreRepository = new GenreEloquentRepository(genreModel: new GenreModel());
        $this->deleteGenreUseCase = new DeleteGenreUseCase(genreRepository: $this->genreRepository);
    }

    public function testShouldBeAbleToDeleteAGenre()
    {
        $genre = GenreModel::factory()->create();
        $deleteGenreInputDTO = new GenreInputDTO(id: $genre->id);

        $response = $this->deleteGenreUseCase->execute(input: $deleteGenreInputDTO);

        $this->assertTrue($response->success);
        $this->assertSoftDeleted('genres', ['id' => $genre->id]);
    }
}
