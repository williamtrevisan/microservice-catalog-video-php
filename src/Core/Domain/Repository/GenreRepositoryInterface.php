<?php

namespace Core\Domain\Repository;

use Core\Domain\Entity\Genre;

interface GenreRepositoryInterface
{
    public function insert(Genre $genreEntity): Genre;
    public function findById(string $id): Genre;
    public function findAll(string $filter = '', string $order = 'DESC'): array;
    public function paginate(
        string $filter = '',
        string $order = 'DESC',
        int $page = 1,
        int $totalPage = 15
    ): PaginationInterface;
    public function update(Genre $genreEntity): Genre;
    public function delete(string $id): bool;
    public function toGenre(object $data): Genre;
}
