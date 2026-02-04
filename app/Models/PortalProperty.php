<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalProperty extends Model
{
    use HasFactory;

    protected $table='portal_property';
    protected $guarded = [];
}
