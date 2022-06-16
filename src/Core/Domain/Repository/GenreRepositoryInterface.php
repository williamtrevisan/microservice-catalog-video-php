<?php

namespace Core\Domain\Repository;

interface GenreRepositoryInterface extends RepositoryInterface
{
    public function getIdsByListId(array $genresId = []): array;
}
