<?php

namespace Core\Domain\Repository;

interface CastMemberRepositoryInterface extends RepositoryInterface
{
    public function getIdsByListId(array $castMembersId = []): array;
}
