<?php

namespace App\Repositories\Presenters;

use Core\Domain\Repository\PaginationInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use stdClass;

class PaginationPresenter implements PaginationInterface
{
    public function __construct(protected LengthAwarePaginator $paginator) {}

    /**
     * @return stdClass[]
     */
    public function items(): array
    {
        return $this->resolveItems($this->paginator->items()) ?? [];
    }

    public function total(): int
    {
        return $this->paginator->total() ?? 0;
    }

    public function currentPage(): int
    {
        return $this->paginator->currentPage() ?? 0;
    }

    public function firstPage(): int
    {
        return $this->paginator->firstItem() ?? 0;
    }

    public function lastPage(): int
    {
        return $this->paginator->lastPage() ?? 0;
    }

    public function perPage(): int
    {
        return $this->paginator->perPage() ?? 0;
    }

    public function to(): int
    {
        return $this->paginator->firstItem() ?? 0;
    }

    public function from(): int
    {
        return $this->paginator->lastItem() ?? 0;
    }

    private function resolveItems(array $items): array
    {
        $itemsResolved = [];

        foreach($items as $item) {
            $objectOfItems = new stdClass();

            foreach ($item->toArray() as $key => $value) $objectOfItems->{$key} = $value;

            $itemsResolved[] = $objectOfItems;
        }

        return $itemsResolved;
    }
}
