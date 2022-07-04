<?php

namespace App\Repositories\Eloquent;

use App\Models\Video as VideoModel;
use Core\Domain\Entity\BaseEntity;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\Repository\VideoRepositoryInterface;

class VideoEloquentRepository implements VideoRepositoryInterface
{
    public function __construct(protected readonly VideoModel $videoModel)
    {
    }

    public function insert(BaseEntity $baseEntity): BaseEntity
    {
        // TODO: Implement insert() method.
    }

    public function findById(string $id): BaseEntity
    {
        // TODO: Implement findById() method.
    }

    public function findAll(string $filter = '', string $order = 'DESC'): array
    {
        // TODO: Implement findAll() method.
    }

    public function paginate(string $filter = '', string $order = 'DESC', int $page = 1, int $totalPage = 15): PaginationInterface
    {
        // TODO: Implement paginate() method.
    }

    public function update(BaseEntity $baseEntity): BaseEntity
    {
        // TODO: Implement update() method.
    }

    public function delete(string $id): bool
    {
        // TODO: Implement delete() method.
    }

    public function updateMedia(BaseEntity $baseEntity): BaseEntity
    {
        // TODO: Implement updateMedia() method.
    }
}
