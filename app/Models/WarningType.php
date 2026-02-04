<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarningType extends Model
{
    use HasFactory;

    protected $table='warnings_type';
    protected $guarded = [];
}
