<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $link_id
 * @property string $name
 */
class Tag extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['name'];
}
