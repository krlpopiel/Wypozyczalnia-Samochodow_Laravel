<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalStatus extends Model
{
    public $timestamps = false; // Zazwyczaj słowniki nie potrzebują timestamps, ale w migracji je dodałem, więc można zostawić true (domyślnie).
    
    protected $fillable = ['name', 'label'];

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }
}