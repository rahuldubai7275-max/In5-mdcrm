<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HRRequest extends Model
{
    use HasFactory;

    protected $table='hr_requests';
    protected $guarded = [];
}
