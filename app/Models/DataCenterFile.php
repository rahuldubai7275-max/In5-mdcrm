<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataCenterFile extends Model
{
    use HasFactory;

    protected $table='data_center_file';
    protected $guarded = [];
}
