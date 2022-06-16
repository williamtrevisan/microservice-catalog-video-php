<?php

namespace Core\Domain\Repository;

interface CategoryRepositoryInterface extends RepositoryInterface
{
    public function getIdsByListId(array $categoriesId = []): array;
}
