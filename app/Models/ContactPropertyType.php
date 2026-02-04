<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactPropertyType extends Model
{
    use HasFactory;

    protected $table='contact_property_type';
    public $timestamps = false;
    protected $guarded=[];
    
    public function Contact(){
        return $this->belongsTo('App\Models\Contact','contact_id');
    }
}
