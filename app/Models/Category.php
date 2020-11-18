<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\NotFoundApiException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['name'];

    public static function boot(): void
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function resolveRouteBinding($value, $field = null): Category
    {
        $category = $this->where('id', $value)->first();

        if (!$category) {
            throw new NotFoundApiException('Category not found');
        }

        return $category;
    }

    /** Relationships **/
    public function products(): HasMany
    {
        return $this->hasMany(ProductCategory::class);
    }
}
