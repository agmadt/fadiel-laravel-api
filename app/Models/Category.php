<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use App\Exceptions\NotFoundApiException;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['name'];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $product = $this->where('id', $value)->first();

        if (!$product) {
            throw new NotFoundApiException('Category not found');
        }

        return $product;
    }
}
