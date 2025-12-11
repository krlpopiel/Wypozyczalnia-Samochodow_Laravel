<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_id', 'car_type_id', 'branch_id', 'model', 
        'registration_plate', 'year', 'color', 'mileage', 
        'daily_rate', 'image_path', 'is_available'
    ];

    // Relacje
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function type()
    {
        return $this->belongsTo(CarType::class, 'car_type_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function features()
    {
        return $this->belongsToMany(Feature::class);
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    // Scope do filtrowania dostępnych aut
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }
}