<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyStatusHistory extends Model
{
    use HasFactory;

    protected $table='property_status_history';
    protected $guarded = [];
}

