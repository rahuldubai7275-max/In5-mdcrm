<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactNote extends Model
{
    use HasFactory;

    protected $table='contact_note';
    protected $guarded = [];

    public function Admin(){
        return $this->belongsTo('App\Models\Admin','admin_id');
    }
}
