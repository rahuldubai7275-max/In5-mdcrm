<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class History extends Authenticatable
{
    use HasFactory;

    protected $table='history';
    protected $guarded = [];

}
