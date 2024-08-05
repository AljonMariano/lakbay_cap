<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservations extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'reservations';
    protected $primaryKey = 'reservation_id';
    protected $fillable=[
        'rs_passengers', 'rs_travel_type', 'rs_purpose', 'rs_from',
        'rs_date_start', 'rs_time_start', 'rs_date_end', 'rs_time_end',
        'reason', 'destination_activity', 'rs_approval_status', 'rs_status',
        'is_outsider', 'outside_office', 'outside_requestor',
        'off_id', 'requestor_id'
    ];
    protected $casts = [
        'is_outsider' => 'boolean',
        'requestor_id' => 'integer',
        'off_id' => 'integer',
    ];
    public function requestors(): BelongsTo
    {
        return $this->belongsTo(Requestors::class, 'requestor_id', 'requestor_id');
    }
    public function reservation_vehicles()
    {
        return $this->hasMany(ReservationVehicle::class, 'reservation_id', 'reservation_id');
    }
    public function office(): BelongsTo
    {
        return $this->belongsTo(Offices::class, 'off_id', 'off_id');
    }

    public function drivers(): HasManyThrough
    {
        return $this->hasManyThrough(
            Drivers::class,
            ReservationVehicle::class,
            'reservation_id', 
            'driver_id',
            'reservation_id', 
            'driver_id' 
        );
    }

}