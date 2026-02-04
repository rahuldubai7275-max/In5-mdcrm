<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    use HasFactory;

    protected $table='community';
    protected $fillable=[
        'master_project_id',
        'name'
    ];

    public function MasterProject(){
        return $this->belongsTo('App\Models\MasterProject','master_project_id');
    }

    public function VillaType(){
        return $this->hasMany('App\Models\VillaType','community_id');
    }

    public function ClusterStreet(){
        return $this->hasMany('App\Models\ClusterStreet','community_id');
    }

    public function VillaNumber(){
        return $this->hasMany('App\Models\VillaNumber','community_id');
    }
}
