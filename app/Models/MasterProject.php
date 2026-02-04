<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterProject extends Model
{
    use HasFactory;

    protected $table='master_project';
    protected $fillable=[
        'emirate_id',
        'name'
    ];
    public function Emirate(){
        return $this->belongsTo('App\Models\Emirate','emirate_id');
    }

    public function Properties(){
        return $this->hasMany('App\Models\Property','master_project_id');
    }

    public function Community(){
        return $this->hasMany('App\Models\Community','master_project_id');
    }
}
