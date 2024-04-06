<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warp extends Model
{
    use HasFactory;

    protected $fillable = [
        'fabric_cost_id',
        'yarn_name',
        'ends',
        'weight',
        'rate',
        'amount',
        'denier'
    ];

    public function WarpWeight(){
        return $this->ends * $this->yarn_denier * 110 / 900000000;
    }
}
