<?php

namespace Core\Domain\Validation;

use Core\Domain\Exception\EntityValidationException;

class DomainValidation
{
    public static function notNull(string $value, string $exceptionMessage = null)
    {
        if (! $value) {
            throw new EntityValidationException(
                $exceptionMessage ?? 'Should not be empty or null'
            );
        }
    }

    public static function strMaxLength(
        string $value,
        int $length = 255,
        string $exceptionMessage = null
    ) {
        if (strlen($value) <= $length) {
            throw new EntityValidationException(
                $exceptionMessage ?? "The value must not be greater than $length characters"
            );
        }
    }
}