<?php

namespace App\Api\V1\Requests;

use App\Api\V1\Requests\BaseApiRequest;

class UpdateProductRequest extends BaseApiRequest
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
            'name' => 'sometimes|required',
            'price' => 'sometimes|required',
            'images' => 'array|min:1',
            'images.*.image' => 'required',
            'variants' => 'array|min:1',
            'variants.*.name' => 'required',
            'variants.*.options' => 'required|array',
            'variants.*.options.*.name' => 'required',
            'categories' => 'array|min:1',
            'categories.*.id' => 'required',
        ];
    }
}
