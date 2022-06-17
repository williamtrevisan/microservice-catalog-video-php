<?php

namespace Core\Domain\Builder;

use Core\Domain\Entity\BaseEntity;

interface BuilderInterface
{
    public function createEntity(object $input): void;
    public function getEntity(): BaseEntity;
}
