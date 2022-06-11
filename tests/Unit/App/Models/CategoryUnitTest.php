<?php

namespace Tests\Unit\App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class CategoryUnitTest extends TestCase
{
    protected function model(): Model
    {
        return new Category();
    }

    public function testIfUseTraits()
    {
        $expectedTraits = [HasFactory::class, SoftDeletes::class];
        $traits = class_uses($this->model());

        $this->assertEquals($expectedTraits, array_keys($traits));
    }

    public function testIncrementingIsFalse()
    {
        $incrementing = $this->model()->incrementing;

        $this->assertFalse($incrementing);
    }

    public function testHasCasts()
    {
        $expectedCasts = [
            'id' => 'string',
            'is_active' => 'boolean',
            'deleted_at' => 'datetime',
        ];
        $casts = $this->model()->getCasts();

        $this->assertEquals($expectedCasts, $casts);
    }

    public function testFillables()
    {
        $expectedFillables = [
            'id',
            'name',
            'description',
            'is_active',
        ];
        $fillables = $this->model()->getFillable();

        $this->assertEquals($expectedFillables, $fillables);
    }
}
