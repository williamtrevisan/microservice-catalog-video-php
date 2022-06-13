<?php

namespace Tests\Unit\App\Models;

use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;

abstract class ModelTestCase extends TestCase
{
    abstract protected function getModel(): Model;
    abstract protected function getTraits(): array;
    abstract protected function getFillables(): array;
    abstract protected function getCasts(): array;

    public function testIncrementingIsFalse()
    {
        $this->assertFalse($this->getModel()->incrementing);
    }

    public function testIfUseTraits()
    {
        $expectedTraits = $this->getTraits();
        $traits = class_uses($this->getModel());

        $this->assertEquals($expectedTraits, array_keys($traits));
    }

    public function testFillables()
    {
        $expectedFillables = $this->getFillables();
        $fillables = $this->getModel()->getFillable();

        $this->assertEquals($expectedFillables, $fillables);
    }

    public function testHasCasts()
    {
        $expectedCasts = $this->getCasts();
        $casts = $this->getModel()->getCasts();

        $this->assertEquals($expectedCasts, $casts);
    }
}
