<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
            $model->id = (string) Uuid::generate(4);
        });
    }

    /** Accessor **/
    public function getCreatedAtTimestampAttribute()
    {
        return $this->created_at;
    }
}
