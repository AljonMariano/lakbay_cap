<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicles extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $primaryKey = 'vehicle_id';

    // Define fillable fields
    protected $fillable = [
        'vh_plate',
        'vh_type',
        'vh_brand',
        'vh_year',
        'vh_fuel_type',
        'vh_condition',
        'vh_capacity',
        'vh_status',
        'vh_confirmation',
    ];

    // Define relationships
    public function reservations(): HasMany
    {
        return $this->belongsToMany(Reservations::class, 'reservation_vehicle', 'vehicle_id', 'reservation_id');
    }

    // Define fuel types as constants
    const FUEL_TYPES = [
        'Diesel',
        'Gasoline',
        'Electric',
    ];

    // Define status options as constants
    const STATUS_OPTIONS = [
        'Available',
        'Not Available',
        'For Maintenance',
    ];
}