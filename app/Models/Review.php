<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'review',
        'reviewable_type',
        'reviewable_id'
    ];

    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }
}
