<?php

namespace UseCase\Genre;

use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\UseCase\DTO\Genre\List\ListGenresInputDTO;
use Core\UseCase\DTO\Genre\List\ListGenresOutputDTO;
use Core\UseCase\Genre\ListGenresUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class ListGenresUseCaseUnitTest extends TestCase
{
    public function testListGenresUseCase()
    {
        $genreRepository = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $genreRepository
            ->shouldReceive('paginate')
            ->once()
            ->andReturn($this->getGenrePagination());
        $listGenresInputDTO = Mockery::mock(ListGenresInputDTO::class,
            ['', 'DESC', 1, 15]
        );

        $listGenresUseCase = new ListGenresUseCase($genreRepository);
        $response = $listGenresUseCase->execute($listGenresInputDTO);

        $this->assertInstanceOf(ListGenresOutputDTO::class, $response);

        Mockery::close();
    }

    private function getGenrePagination(array $items = [])
    {
        $genrePagination = Mockery::mock(stdClass::class, PaginationInterface::class);
        $genrePagination->shouldReceive('items')->andReturn($items);
        $genrePagination->shouldReceive('total')->andReturn(0);
        $genrePagination->shouldReceive('currentPage')->andReturn(0);
        $genrePagination->shouldReceive('firstPage')->andReturn(0);
        $genrePagination->shouldReceive('lastPage')->andReturn(0);
        $genrePagination->shouldReceive('perPage')->andReturn(0);
        $genrePagination->shouldReceive('to')->andReturn(0);
        $genrePagination->shouldReceive('from')->andReturn(0);

        return $genrePagination;
    }
}
