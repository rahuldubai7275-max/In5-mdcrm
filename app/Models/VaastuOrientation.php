<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VaastuOrientation extends Model
{
    use HasFactory;

    protected $table='vaastu_orientation';
    protected $fillable=[
        'name'
    ];
}
