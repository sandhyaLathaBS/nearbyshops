<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shops extends Model
{
    use HasFactory, SoftDeletes;
    public function shopFunctionalTimings()
    {
        return $this->hasMany(ShopTimings::class, 'storeId', 'id')
            ->where('status', 1)
            ->where('breakStatus', 0);
    }
    public function shopBreakTimings()
    {
        return $this->hasOne(ShopTimings::class, 'storeId', 'id')
            ->where('status', 2)
            ->where('breakStatus', 1);
    }
}