<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = [
        'position',
        'title',
        'message',
        'created_by',
        'published_at',
        'expires_at',
        'created_at',
        'is_pinned',
        'audience'
    ];

    protected function casts() : array{
        return [
           'published_at' => 'datetime',
            'expires_at'   => 'datetime',
            'is_pinned'    => 'boolean',
        ];
    }

    // -----------------------  RELATIONS  ---------------------
    public function user() : BelongsTo{
        return $this->belongsTo(User::class, 'created_by');
    }
}