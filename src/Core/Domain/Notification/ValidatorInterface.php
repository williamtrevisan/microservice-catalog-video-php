<?php

namespace Core\Domain\Notification;

use Core\Domain\Entity\BaseEntity;

interface ValidatorInterface
{
    public function validate(BaseEntity $baseEntity): void;
}
