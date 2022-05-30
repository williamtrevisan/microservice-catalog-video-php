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

    public function testStrMaxLength()
    {
        try {
            $value = 'Test';

            DomainValidation::strMaxLength($value, 3);

            $this->assertTrue(false);
        } catch (Throwable $throwable) {
            $this->assertInstanceOf(EntityValidationException::class, $throwable);
        }
    }

    public function testStrMaxLengthCustomExceptionMessage()
    {
        try {
            $value = 'Test';

            DomainValidation::strMaxLength($value, 3, 'Custom error message');

            $this->assertTrue(false);
        } catch (Throwable $throwable) {
            $this->assertInstanceOf(
                EntityValidationException::class,
                $throwable,
                'Custom error message'
            );
        }
    }

    public function testStrMinLength()
    {
        try {
            $value = 'Test';

            DomainValidation::strMinLength($value, 6);

            $this->assertTrue(false);
        } catch (Throwable $throwable) {
            $this->assertInstanceOf(EntityValidationException::class, $throwable);
        }
    }

    public function testStrMinLengthCustomExceptionMessage()
    {
        try {
            $value = 'Test';

            DomainValidation::strMinLength($value, 6, 'Custom error message');

            $this->assertTrue(false);
        } catch (Throwable $throwable) {
            $this->assertInstanceOf(
                EntityValidationException::class,
                $throwable,
                'Custom error message'
            );
        }
    }

    public function testStrCanNullAndMaxLength()
    {
        try {
            $value = 'Test';

            DomainValidation::strCanNullAndMaxLength($value, 3);

            $this->assertTrue(false);
        } catch (Throwable $throwable) {
            $this->assertInstanceOf(EntityValidationException::class, $throwable);
        }
    }

    public function testStrCanNullAndMaxLengthSendingNull()
    {
        try {
            $value = '';

            DomainValidation::strCanNullAndMaxLength($value, 3);

            $this->assertTrue(false);
        } catch (Throwable $throwable) {
            $this->assertNotInstanceOf(EntityValidationException::class, $throwable);
        }
    }

    public function testStrCanNullAndMaxLengthCustomExceptionMessage()
    {
        try {
            $value = 'Test';

            DomainValidation::strCanNullAndMaxLength($value, 3, 'Custom error message');

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