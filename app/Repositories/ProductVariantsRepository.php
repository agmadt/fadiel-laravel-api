<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductVariantsRepository
{
    public function deleteAllVariantsFromProduct(Product $product): bool
    {
        DB::beginTransaction();

        foreach ($product->variants as $variant) {
            foreach ($variant->options as $option) {
                $option->delete();
            }

            $variant->delete();
        }

        DB::commit();

        return true;
    }
}
