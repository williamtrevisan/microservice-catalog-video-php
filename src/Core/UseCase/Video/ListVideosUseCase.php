<?php

namespace Core\UseCase\Video;

use Core\Domain\Repository\VideoRepositoryInterface;
use Core\UseCase\DTO\Video\List\ListVideosInputDTO;
use Core\UseCase\DTO\Video\List\ListVideosOutputDTO;

class ListVideosUseCase
{
    public function __construct(
        protected readonly VideoRepositoryInterface $videoRepository
    ) {}

    public function execute(ListVideosInputDTO $input): ListVideosOutputDTO
    {
        $videos = $this->videoRepository->paginate(
            filter: $input->filter,
            order: $input->order,
            page: $input->page,
            totalPage: $input->totalPage,
        );

        return new ListVideosOutputDTO(
            items: $videos->items(),
            total: $videos->total(),
            current_page: $videos->currentPage(),
            first_page: $videos->firstPage(),
            last_page: $videos->lastPage(),
            per_page: $videos->perPage(),
            to: $videos->to(),
            from: $videos->from(),
        );
    }
}
