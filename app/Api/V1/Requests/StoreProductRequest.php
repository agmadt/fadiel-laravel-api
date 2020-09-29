<?php

namespace App\Api\V1\Requests;

use Illuminate\Http\Response;
use App\Api\V1\Requests\BaseApiRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreProductRequest extends BaseApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'price' => 'required',
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
