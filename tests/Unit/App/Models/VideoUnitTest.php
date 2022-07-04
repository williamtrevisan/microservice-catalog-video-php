<?php

namespace Tests\Unit\App\Models;

use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoUnitTest extends ModelTestCase
{
    protected function getModel(): Model
    {
        return new Video();
    }

    protected function getTraits(): array
    {
        return [HasFactory::class, SoftDeletes::class];
    }

    protected function getFillables(): array
    {
        return [
            'id',
            'title',
            'description',
            'year_launched',
            'opened',
            'rating',
            'duration',
            'created_at',
        ];
    }

    protected function getCasts(): array
    {
        return [
            'id' => 'string',
            'deleted_at' => 'datetime',
        ];
    }
}
