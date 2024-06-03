<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Models\Events;
use App\Models\Drivers;
use App\Models\Requestors;
use App\Models\Vehicles;
use App\Models\ReservationVehicle;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservations extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $primaryKey = 'reservation_id';
    protected $fillable=[
        'rs_voucher',
        'rs_daily_transport',
        'rs_cancelled',
        'rs_outside_province',
        'rs_date_filed',
        'rs_passengers',
        'rs_approval_status',
        'rs_status',
        'event_id',
        'requestor_id'
    ];
    public function events(): BelongsTo
    {
        return $this->belongsTo(Events::class, 'event_id');
    }
    public function reservation_vehicles():HasMany
    {
        return $this->hasMany(ReservationVehicle::class,'reservation_id');
    }

    public function requestors(): BelongsTo
    {
        return $this->belongsTo(Requestors::class, 'requestor_id');
    }

    public function vehicles(): HasManyThrough
    {
        return $this->hasManyThrough(
            //
            Vehicles::class,
            ReservationVehicle::class,
            'reservation_id', // Foreign key on the environments table...
            'vehicle_id', // Foreign key on the deployments table...
            'id', // Local key on the projects table...
            'id' // Local key on the environments table...
        );
    }
    public function drivers(): HasManyThrough
    {
        return $this->hasManyThrough(
            Drivers::class,
            ReservationVehicle::class,
            'reservation_id', // Foreign key on the environments table...
            'driver_id', // Foreign key on the deployments table...
            'id', // Local key on the projects table...
            'id' // Local key on the environments table...
        );
    }

}
