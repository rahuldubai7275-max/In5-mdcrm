<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSource extends Model
{
    use HasFactory;
    protected $table='contact_source';
    protected $fillable=[
        'name'
    ];


}
