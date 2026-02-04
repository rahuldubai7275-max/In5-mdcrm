<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorMotivation extends Model
{
    use HasFactory;

    protected $table='vendor_motivation';
    protected $fillable=[
        'name'
    ];
}
