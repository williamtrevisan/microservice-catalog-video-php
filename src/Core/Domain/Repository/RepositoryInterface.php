<?php

namespace Core\Domain\Repository;

use Core\Domain\Entity\BaseEntity;

interface RepositoryInterface
{
    public function insert(BaseEntity $baseEntity): BaseEntity;
    public function findById(string $id): BaseEntity;
    public function findAll(string $filter = '', string $order = 'DESC'): array;
    public function paginate(
        string $filter = '',
        string $order = 'DESC',
        int $page = 1,
        int $totalPage = 15
    ): PaginationInterface;
    public function update(BaseEntity $baseEntity): BaseEntity;
    public function delete(string $id): bool;
}
