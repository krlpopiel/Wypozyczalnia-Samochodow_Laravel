<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'icon_class'];

    public function cars()
    {
        return $this->belongsToMany(Car::class);
    }
}