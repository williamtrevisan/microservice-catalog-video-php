<?php

namespace Tests\Unit\App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryUnitTest extends ModelTestCase
{
    protected function getModel(): Model
    {
        return new Category();
    }

    protected function getTraits(): array
    {
        return [HasFactory::class, SoftDeletes::class];
    }

    protected function getFillables(): array
    {
        return [
            'id',
            'name',
            'description',
            'is_active',
        ];
    }

    protected function getCasts(): array
    {
        return [
            'id' => 'string',
            'is_active' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }
}
