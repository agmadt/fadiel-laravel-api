<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductImage extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['image'];
    public $timestamps = false;

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }
}
