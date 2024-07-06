<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Drivers;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Offices extends Model
{
    
    use HasFactory;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $primaryKey = 'off_id';
    protected $fillable=[
        'off_id',
        'off_acr',
        'off_name',
        'off_head',
    ];
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservations::class, 'off_id', 'off_id');
    }
}
