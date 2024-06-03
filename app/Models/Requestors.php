<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Requestors extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $primaryKey = 'requestor_id';
    protected $fillable = [
        'rq_full_name',
        'rq_office',
    ];
}
