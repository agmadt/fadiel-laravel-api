<?php

namespace App\Api\V1\Requests;

use App\Api\V1\Requests\BaseApiRequest;

class StoreOrderRequest extends BaseApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'buyer_name' => 'required',
            'buyer_email' => 'required|email',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required',
            'products.*.quantity' => 'required',
            'products.*.variants' => 'array',
            'products.*.variants.*.variant_id' => 'required',
            'products.*.variants.*.option_id' => 'required'
        ];
    }
}
