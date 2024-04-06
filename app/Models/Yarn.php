<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\YarnCategory;
class Yarn extends Model
{
    use HasFactory;



    protected $fillable = [
        
        'yarn_name',
        'yarn_denier',
        'yarn_rate',
        'category_id',
        'user_id'
    ];

    public function category()
    {
        return $this->belongsTo(YarnCategory::class, 'category_id', 'id');
    }

    
}
