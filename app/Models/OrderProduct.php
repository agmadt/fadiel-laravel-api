<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    /** Accessor **/
    public function getProductJSONAttribute()
    {
        return json_decode($this->product);
    }
}
