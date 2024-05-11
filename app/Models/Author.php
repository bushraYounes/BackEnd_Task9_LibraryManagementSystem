<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;


    protected $fillable = [
        'first_name',
        'last_name'
    ];

    public function books()
    {
        return $this->belongsToMany(Book::class);
    }

    public function getFirstNameAttribute($value)
    {
        return ucfirst($value);
    }
    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = ucfirst($value);
    }

    public function getLastNameAttribute($value)
    {
        return ucfirst($value);
    }
    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = ucfirst($value);
    }

    public function getFullNameAttribute()
    {
        return ucfirst($this->attributes['first_name'])." ".ucfirst($this->attributes['last_name']);
    }
}
