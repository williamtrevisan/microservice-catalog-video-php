<?php

namespace Core\UseCase\Genre;

use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\DTO\Genre\List\ListGenresInputDTO;
use Core\UseCase\DTO\Genre\List\ListGenresOutputDTO;

class ListGenresUseCase
{
    public function __construct(
        protected readonly GenreRepositoryInterface $genreRepository
    ) {}

    public function execute(ListGenresInputDTO $input): ListGenresOutputDTO
    {
        $genres = $this->genreRepository->paginate(
            filter: $input->filter,
            order: $input->order,
            page: $input->page,
            totalPage: $input->totalPage,
        );

        return new ListGenresOutputDTO(
            items: $genres->items(),
            total: $genres->total(),
            current_page: $genres->currentPage(),
            first_page: $genres->firstPage(),
            last_page: $genres->lastPage(),
            per_page: $genres->perPage(),
            to: $genres->to(),
            from: $genres->from(),
        );
    }
}
