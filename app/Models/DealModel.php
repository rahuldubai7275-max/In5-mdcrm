<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealModel extends Model
{
    use HasFactory;

    protected $table='deal_model';
    protected $guarded = [];
}
