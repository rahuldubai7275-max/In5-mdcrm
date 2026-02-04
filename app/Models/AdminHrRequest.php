<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminHrRequest extends Model
{
    use HasFactory;

    protected $table='admin_hr_requests';
    protected $guarded = [];
}
