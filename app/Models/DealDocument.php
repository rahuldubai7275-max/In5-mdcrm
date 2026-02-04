<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealDocument extends Model
{
    use HasFactory;

    protected $table='deal_documents';
    protected $guarded = [];
}
