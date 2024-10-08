<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Offices;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Drivers extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $primaryKey = 'driver_id';
    protected $fillable = [
        'driver_id',
        'dr_emp_id',
        'dr_fname',
        'dr_mname',
        'dr_lname',
        'off_id',
        'dr_status',
    ];
    public function offices(): BelongsTo
    {
        return $this->belongsTo(Offices::class, 'off_id');
    }

    public function reservations()
    {
        return $this->belongsToMany(Reservations::class, 'reservation_vehicles', 'driver_id', 'reservation_id');
    }
}