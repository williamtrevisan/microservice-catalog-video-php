<?php

namespace Tests\Unit\Domain\Validation;

use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Validation\DomainValidation;
use PHPUnit\Framework\TestCase;
use Throwable;

class DomainValidationUnitTest extends TestCase
{
    public function testNotNull()
    {
        try {
            $value = '';

            DomainValidation::notNull($value);

            $this->assertTrue(false);
        } catch (Throwable $throwable) {
            $this->assertInstanceOf(EntityValidationException::class, $throwable);
        }
    }

    public function testNotNullCustomExceptionMessage()
    {
        try {
            $value = '';

            DomainValidation::notNull($value, 'Custom error message');

            $this->assertTrue(false);
        } catch (Throwable $throwable) {
            $this->assertInstanceOf(
                EntityValidationException::class,
                $throwable,
                'Custom error message'
            );
        }
    }
}