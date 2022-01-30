<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int         $id
 * @property string      $hash_id
 * @property string      $title
 * @property string      $long_url
 * @property string      $deleted_at
 * @property-read Tag[]  $tags
 * @property-read View[] $views
 */
class Link extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(View::class);
    }

    public function syncTags(array $tagNames): void
    {
        $this->tags()->delete();

        $tags = array_map(fn(string $tagName) => new Tag(['name' => $tagName]), $tagNames);

        $this->tags()->saveMany($tags);
    }

    public function scopeByHashId(Builder $query, string $hashId): Builder
    {
        return $query->where('hash_id', $hashId);
    }
}
