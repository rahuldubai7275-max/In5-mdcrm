<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClusterStreet extends Model
{
    use HasFactory;

    protected $table='cluster_street';
    protected $guarded = [];

    public function Community(){
        return $this->belongsTo('App\Models\Community','community_id');
    }
}
