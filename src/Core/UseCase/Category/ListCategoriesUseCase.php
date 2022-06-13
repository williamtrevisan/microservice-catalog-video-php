<?php

namespace Core\UseCase\Category;

use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\DTO\Category\list\ListCategoriesInputDTO;
use Core\UseCase\DTO\Category\list\ListCategoriesOutputDTO;

class ListCategoriesUseCase
{
    public function __construct(
        protected readonly CategoryRepositoryInterface $categoryRepository
    ) {}

    public function execute(ListCategoriesInputDTO $input): ListCategoriesOutputDTO
    {
        $categories = $this->categoryRepository->paginate(
            filter: $input->filter,
            order: $input->order,
            page: $input->page,
            totalPage: $input->totalPage,
        );

        return new ListCategoriesOutputDTO(
            items: $categories->items(),
            total: $categories->total(),
            current_page: $categories->currentPage(),
            first_page: $categories->firstPage(),
            last_page: $categories->lastPage(),
            per_page: $categories->perPage(),
            to: $categories->to(),
            from: $categories->from(),
        );
    }
}
