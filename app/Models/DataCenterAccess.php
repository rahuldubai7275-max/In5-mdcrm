<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataCenterAccess extends Model
{
    use HasFactory;

    protected $table='data_center_access';
    protected $guarded = [];
}
