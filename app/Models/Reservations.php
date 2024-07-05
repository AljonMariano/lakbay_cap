<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\ReservationVehicle;

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
        return $this->belongsTo(Events::class, 'event_id', 'event_id');
    }
    public function requestors(): BelongsTo
    {
        return $this->belongsTo(Requestors::class, 'requestor_id', 'requestor_id');
    }
    public function reservation_vehicles(): HasMany
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
            'id',
            'id'
        );
    }

}
