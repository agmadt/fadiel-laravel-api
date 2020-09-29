<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $dates = ['created_at'];
    protected $fillable = ['buyer_name', 'buyer_email', 'total', 'message'];
    public $incrementing = false;
    protected $keyType = 'string';

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function setUpdatedAtAttribute($value)
    {
        // to Disable updated_at
    }

    /** Relationships **/
    public function products()
    {
        return $this->hasMany(OrderProduct::class);
    }
}
