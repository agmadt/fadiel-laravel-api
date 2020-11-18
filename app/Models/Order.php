<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['buyer_name', 'buyer_email', 'total', 'message'];
    public $incrementing = false;
    protected $keyType = 'string';

    public static function boot(): void
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function setUpdatedAtAttribute($value): void
    {
        // to Disable updated_at
    }

    /** Relationships **/
    public function products(): HasMany
    {
        return $this->hasMany(OrderProduct::class);
    }
}
