<?php

namespace UseCase\CastMember;

use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\UseCase\CastMember\ListCastMembersUseCase;
use Core\UseCase\DTO\CastMember\List\ListCastMembersInputDTO;
use Core\UseCase\DTO\CastMember\List\ListCastMembersOutputDTO;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class ListCastMembersUseCaseUnitTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function testListCastMembersUseCase()
    {
        $genreRepository = Mockery::mock(
            stdClass::class,
            CastMemberRepositoryInterface::class
        );
        $genreRepository
            ->shouldReceive('paginate')
            ->once()
            ->andReturn($this->getCastMemberPagination());
        $listCastMembersInputDTO = Mockery::mock(ListCastMembersInputDTO::class,
            ['', 'DESC', 1, 15]
        );

        $listCastMembersUseCase = new ListCastMembersUseCase($genreRepository);
        $response = $listCastMembersUseCase->execute($listCastMembersInputDTO);

        $this->assertInstanceOf(ListCastMembersOutputDTO::class, $response);
    }

    private function getCastMemberPagination(array $items = [])
    {
        $castMemberPagination = Mockery::mock(
            stdClass::class,
            PaginationInterface::class
        );
        $castMemberPagination->shouldReceive('items')->andReturn($items);
        $castMemberPagination->shouldReceive('total')->andReturn(0);
        $castMemberPagination->shouldReceive('currentPage')->andReturn(0);
        $castMemberPagination->shouldReceive('firstPage')->andReturn(0);
        $castMemberPagination->shouldReceive('lastPage')->andReturn(0);
        $castMemberPagination->shouldReceive('perPage')->andReturn(0);
        $castMemberPagination->shouldReceive('to')->andReturn(0);
        $castMemberPagination->shouldReceive('from')->andReturn(0);

        return $castMemberPagination;
    }
}
