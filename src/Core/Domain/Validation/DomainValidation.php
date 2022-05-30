<?php

namespace Core\Domain\Validation;

use Core\Domain\Exception\EntityValidationException;

class DomainValidation
{
    public static function notNull(string $value, string $exceptionMessage = null)
    {
        if (! $value) {
            throw new EntityValidationException($exceptionMessage ?? 'Should not empty or null');
        }
    }
}