<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Events;
use App\Models\Drivers;
use App\Models\Requestors;
use App\Models\ReservationVehicle;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicles extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $primaryKey = 'vehicle_id';
    protected $fillable=[
        'vh_plate',
        'vh_type',
        'vh_brand',
        'vh_year',
        'vh_fuel_type',
        'vh_condition',
        'vh_status',
    ];
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservations::class,'vehicle_id');
    }
    public function reservation_vehicle(): HasMany
    {
        return $this->hasMany(ReservationVehicle::class,'vehicle_id');
    }
    

}
