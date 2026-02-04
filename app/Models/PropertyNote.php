<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyNote extends Model
{
    use HasFactory;

    protected $table='property_note';
    protected $guarded = [];

    public function Admin(){
        return $this->belongsTo('App\Models\Admin','admin_id');
    }
}
