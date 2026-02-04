<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminWarning extends Model
{
    use HasFactory;

    protected $table='admin_warnings';
    protected $guarded = [];
}
