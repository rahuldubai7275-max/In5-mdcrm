<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactCategoryDetail extends Model
{
    use HasFactory;

    protected $table='contact_category_details';
    public $timestamps = false;
    protected $guarded=[];

}
