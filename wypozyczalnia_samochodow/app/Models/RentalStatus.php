<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalStatus extends Model
{
    public $timestamps = false; 
    
    protected $fillable = ['name', 'label'];

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }
}