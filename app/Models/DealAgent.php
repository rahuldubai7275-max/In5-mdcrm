<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealAgent extends Model
{
    use HasFactory;

    protected $table='deal_agents';
    protected $guarded = [];
}
