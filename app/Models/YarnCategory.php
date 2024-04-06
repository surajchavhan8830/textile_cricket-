<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Yarn;

class YarnCategory extends Model
{
    use HasFactory;
    


    protected $fillable = [
        'yarn_category',
        'user_id'
    ];

   
}
