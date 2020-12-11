<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use stdClass;

class OrderProduct extends Model
{
    use HasFactory;

    protected $fillable = ['product'];
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';

    public static function boot(): void
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    /** Accessor **/
    public function getProductJSONAttribute(): stdClass
    {
        return json_decode($this->product);
    }
}
