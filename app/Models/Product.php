<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['name', 'price', 'description'];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    /** Relationships **/
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function categories()
    {
        return $this->hasMany(ProductCategory::class);
    }
}
