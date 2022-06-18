<?php

namespace Tests\Unit\UseCase\Video;

use Core\Domain\Entity\Video;
use Core\Domain\Enum\Rating;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\DTO\Video\List\ListVideosInputDTO;
use Core\UseCase\DTO\Video\List\ListVideosOutputDTO;
use Core\UseCase\Video\ListVideosUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class ListVideosUseCaseUnitTest extends TestCase
{
    protected VideoRepositoryInterface $videoRepository;
    protected ListVideosUseCase $listVideosUseCase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->videoRepository = Mockery::mock(
            stdClass::class, VideoRepositoryInterface::class
        );
        $this->listVideosUseCase = new ListVideosUseCase($this->videoRepository);
    }

    private function createVideoPaginationMock(array $items = [], int $currentPage = 1)
    {
        $videoPagination = Mockery::mock(stdClass::class, PaginationInterface::class);
        $videoPagination
            ->shouldReceive('items')
            ->andReturn($items ? array_chunk($items, 15)[$currentPage - 1] : []);
        $videoPagination->shouldReceive('total')->andReturn(count($items));
        $videoPagination->shouldReceive('currentPage')->andReturn($currentPage);
        $videoPagination->shouldReceive('firstPage')->andReturn(1);
        $videoPagination
            ->shouldReceive('lastPage')
            ->andReturn(count(array_chunk($items, 15)) ?? 1);
        $videoPagination->shouldReceive('perPage')->andReturn(15);
        $videoPagination->shouldReceive('to')->andReturn(1);
        $videoPagination->shouldReceive('from')->andReturn(1);

        return $videoPagination;
    }

    private function arrayVideosEntityMock(int $count = 1): array
    {
        return array_merge(...array_fill(0, $count, $this->getArrayVideoEntityMock()));
    }

    private function getArrayVideoEntityMock(): array
    {
        return [
            Mockery::mock(Video::class, [
                'Video title',
                'Video description',
                2001,
                190,
                true,
                Rating::L,
            ])
        ];
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /** @test */
    public function should_be_able_to_get_a_empty_videos_list()
    {
        $this->videoRepository
            ->shouldReceive('paginate')
            ->once()
            ->andReturn($this->createVideoPaginationMock());
        $listVideosInputDTO = Mockery::mock(ListVideosInputDTO::class, [
            '', 'DESC', 1, 15
        ]);

        $response = $this->listVideosUseCase->execute($listVideosInputDTO);

        $this->assertInstanceOf(ListVideosOutputDTO::class, $response);
        $this->assertEmpty($response->items);
        $this->assertEquals(0, $response->total);
    }

    /** @test */
    public function should_be_able_to_get_a_paginate_videos_list()
    {
        $this->videoRepository
            ->shouldReceive('paginate')
            ->once()
            ->andReturn(
                $this->createVideoPaginationMock($this->arrayVideosEntityMock(count: 16))
            );
        $listVideosInputDTO = Mockery::mock(ListVideosInputDTO::class, [
            '', 'DESC', 1, 15
        ]);

        $response = $this->listVideosUseCase->execute($listVideosInputDTO);

        $this->assertInstanceOf(ListVideosOutputDTO::class, $response);
        $this->assertCount(15, $response->items);
        $this->assertEquals(16, $response->total);
        $this->assertEquals(1, $response->current_page);
        $this->assertEquals(2, $response->last_page);
    }

    /** @test */
    public function should_be_able_to_get_a_paginate_videos_list_on_page_two()
    {
        $this->videoRepository
            ->shouldReceive('paginate')
            ->once()
            ->andReturn(
                $this->createVideoPaginationMock(
                    items: $this->arrayVideosEntityMock(count: 16),
                    currentPage: 2
                )
            );
        $listVideosInputDTO = Mockery::mock(ListVideosInputDTO::class, [
            '', 'DESC', 1, 15
        ]);

        $response = $this->listVideosUseCase->execute($listVideosInputDTO);

        $this->assertInstanceOf(ListVideosOutputDTO::class, $response);
        $this->assertCount(1, $response->items);
        $this->assertEquals(16, $response->total);
        $this->assertEquals(2, $response->current_page);
        $this->assertEquals(2, $response->last_page);
    }
}
