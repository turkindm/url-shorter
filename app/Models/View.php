<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $link_id
 * @property string $user_agent
 * @property string $user_ip
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class View extends Model
{
    public $incrementing = false;

    protected $fillable = ['user_agent', 'user_ip'];
}
