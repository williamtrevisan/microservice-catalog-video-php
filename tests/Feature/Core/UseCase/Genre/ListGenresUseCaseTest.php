<?php

namespace Core\UseCase\Genre;

use App\Models\Genre as GenreModel;
use App\Repositories\Eloquent\GenreEloquentRepository;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\DTO\Genre\List\ListGenresInputDTO;
use Core\UseCase\Genre\ListGenresUseCase;
use Tests\TestCase;

class ListGenresUseCaseTest extends TestCase
{
    protected GenreRepositoryInterface $genreRepository;
    protected ListGenresUseCase $findGenresUseCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->genreRepository = new GenreEloquentRepository(genreModel: new GenreModel());
        $this->findGenresUseCase = new ListGenresUseCase(genreRepository: $this->genreRepository);
    }

    public function testShouldBeAbleToFindAllGenres()
    {
        GenreModel::factory(18)->create();
        $listGenresInputDTO = new ListGenresInputDTO();

        $response = $this->findGenresUseCase->execute(input: $listGenresInputDTO);

        $this->assertCount(15, $response->items);
        $this->assertEquals(18, $response->total);
    }

}
