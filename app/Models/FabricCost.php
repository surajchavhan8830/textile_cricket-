<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FabricCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'fabric_name',
        'warp_yarn',
        'weft_yarn',
        'width',
        'final_ppi',
        'warp_wastage',
        'weft_wastage',
        'butta_cutting_cost',
        'additional_cost',
        'fabric_category_id',
        'user_id',
    ];

    public function category()
    {
        return $this->belongsTo(FabricCategory::class, 'fabric_category_id', 'id');
    }

    public function getculation()
    {
        return $this->additional_cost * $this->fabric_category_id;
    }

}
