<?php

namespace App\Models;

use App\Enums\ImageTypes;
use App\Enums\MediaTypes;
use Core\Domain\ValueObject\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Video extends Model
{
    use HasFactory;

    public function castMembers(): BelongsToMany
    {
        return $this->belongsToMany(CastMember::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    public function media(): HasOne
    {
        return $this->hasOne(Media::class)->where('type', MediaTypes::Video->value);
    }

    public function trailer(): HasOne
    {
        return $this->hasOne(Media::class)->where('type', MediaTypes::Trailer->value);
    }

    public function banner(): HasOne
    {
        return $this->hasOne(ImageVideo::class)
            ->where('type', ImageTypes::Banner->value);
    }

    public function thumb(): HasOne
    {
        return $this->hasOne(ImageVideo::class)
            ->where('type', ImageTypes::Thumb->value);
    }

    public function thumbHalf(): HasOne
    {
        return $this->hasOne(ImageVideo::class)
            ->where('type', ImageTypes::ThumbHalf->value);
    }
}
