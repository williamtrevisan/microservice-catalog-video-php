<?php

namespace Core\UseCase\DTO\Category\list;

class ListCategoriesOutputDTO
{
    public function __construct(
        public array $items,
        public int $total,
        public int $currentPage,
        public int $firstPage,
        public int $lastPage,
        public int $perPage,
        public int $to,
        public int $from,
    ) {}
}