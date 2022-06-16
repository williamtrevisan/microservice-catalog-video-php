<?php

namespace Tests\Unit\App\Models;

use App\Models\CastMember;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CastMemberUnitTest extends ModelTestCase
{
    protected function getModel(): CastMember
    {
        return new CastMember();
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
            'type',
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
