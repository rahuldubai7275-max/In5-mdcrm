<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VillaType extends Model
{
    use HasFactory;

    protected $table='villa_type';
    protected $guarded = [];

    public function Community(){
        return $this->belongsTo('App\Models\Community','community_id');
    }
}
