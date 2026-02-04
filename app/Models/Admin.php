<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
class Admin extends Authenticatable
{
    use HasFactory;

    protected $table='admins';
    protected $guard = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function PropertyNote(){
        return $this->hasMany('App\Models\PropertyNote','admin_id');
    }

    public function ContactNote(){
        return $this->hasMany('App\Models\ContactNote','admin_id');
    }

    public function LeadNote(){
        return $this->hasMany('App\Models\LeadNote','admin_id');
    }

    public function DataCenterNote(){
        return $this->hasMany('App\Models\DataCenterNote','admin_id');
    }
}
