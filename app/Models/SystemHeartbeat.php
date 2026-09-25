<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemHeartbeat extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'ran_at' => 'datetime',
        ];
    }
}
