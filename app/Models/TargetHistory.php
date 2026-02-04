<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetHistory extends Model
{
    use HasFactory;

    protected $table='targets_history';
    protected $guarded = [];
}
