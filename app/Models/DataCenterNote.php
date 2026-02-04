<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataCenterNote extends Model
{
    use HasFactory;

    protected $table='data_center_note';
    protected $guarded = [];

    public function Admin(){
        return $this->belongsTo('App\Models\Admin','admin_id');
    }
}
