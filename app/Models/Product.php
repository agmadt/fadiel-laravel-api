<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Exceptions\NotFoundApiException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function resolveRouteBinding($value, $field = null): Product
    {
        $product = $this->where('id', $value)->first();

        if (!$product) {
            throw new NotFoundApiException('Product not found');
        }

        return $product;
    }

    /** Relationships **/
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(ProductCategory::class);
    }
}
