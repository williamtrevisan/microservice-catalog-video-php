<?php

namespace Core\Domain\Repository;

use Core\Domain\Entity\BaseEntity;

interface VideoRepositoryInterface extends RepositoryInterface
{
    public function updateMedia(BaseEntity $baseEntity): BaseEntity;
}
