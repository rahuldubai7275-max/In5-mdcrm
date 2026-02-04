<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Contact extends Authenticatable
{
    use HasFactory;

    protected $table='contacts';
    protected $guard = 'contact-admin';
    protected $guarded = [];

    public function PropertyNote(){
        return $this->hasMany('App\Models\PropertyNote','contact_id');
    }

    public function ContactBedroom(){
        return $this->hasMany('App\Models\ContactBedroom','contact_id');
    }

    public function ContactMasterProject(){
        return $this->hasMany('App\Models\ContactMasterProject','contact_id');
    }

    public function ContactCommunity(){
        return $this->hasMany('App\Models\ContactCommunity','contact_id');
    }

    public function ContactPropertyType(){
        return $this->hasMany('App\Models\ContactPropertyType','contact_id');
    }
}
