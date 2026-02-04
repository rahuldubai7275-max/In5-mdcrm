<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityParent extends Model
{
    use HasFactory;

    protected $table='community_parent';
    protected $guarded = [];
}
