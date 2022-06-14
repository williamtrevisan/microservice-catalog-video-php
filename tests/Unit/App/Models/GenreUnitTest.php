<?php

namespace Tests\Unit\App\Models;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GenreUnitTest
{
    protected function getModel(): Model
    {
        return new Genre();
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
            'is_active',
            'created_at',
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
