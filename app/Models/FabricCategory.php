<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FabricCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'fabric_category',
        'user_id'
    ];
}
