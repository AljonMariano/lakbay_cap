<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Events;
use App\Models\Drivers;
use App\Models\Requestors;
use App\Models\Vehicles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationVehicle extends Model
{
    use HasFactory;
    protected $table = 'reservation_vehicles';
    protected $fillable=[
        'reservation_id',
        'driver_id',
        'vehicle_id'
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservations::class, 'reservation_id', 'reservation_id');
    }

    public function drivers(): BelongsTo
    {
        return $this->belongsTo(Drivers::class, 'driver_id');
    }

    public function vehicles(): BelongsTo
    {
        return $this->belongsTo(Vehicles::class, 'vehicle_id');
    }
}