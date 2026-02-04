<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadNote extends Model
{

    protected $table='lead_notes';
    protected $guarded = [];

    public function Admin(){
        return $this->belongsTo('App\Models\Admin','admin_id');
    }

}
