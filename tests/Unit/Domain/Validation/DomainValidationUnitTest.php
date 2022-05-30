<?php

namespace Tests\Unit\Domain\Validation;

use Core\Domain\Exception\EntityValidationException;
use PHPUnit\Framework\TestCase;
use Throwable;

class DomainValidationUnitTest extends TestCase
{
    public function testNotNull()
    {
        try {
            $value = '';

            DomainValidationUnitTest::notNull($value);

            $this->assertTrue(false);
        } catch (Throwable $throwable) {
            $this->assertInstanceOf(EntityValidationException::class, $throwable);
        }
    }
}