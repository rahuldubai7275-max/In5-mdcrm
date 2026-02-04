<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactBedroom extends Model
{
    use HasFactory;

    protected $table='contact_bedroom';
    public $timestamps = false;
    protected $guarded=[];
    
    public function Contact(){
        return $this->belongsTo('App\Models\Contact','contact_id');
    }
}
