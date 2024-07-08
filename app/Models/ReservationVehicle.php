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
    protected $fillable=[
        'vehicle_id',
        'reservation_id',
        'driver_id'
    ];

    public function vehicles(): BelongsTo
    {
        return $this->belongsTo(Vehicles::class, 'vehicle_id');
    }

    public function drivers(): BelongsTo
    {
        return $this->belongsTo(Drivers::class, 'driver_id');
    }
}