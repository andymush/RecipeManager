<?php

namespace App\Http\Requests\Recipes\Recipe;

use App\Models\Recipes\Recipe;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Controllers\Recipes\TagController;
use App\Http\Controllers\Recipes\CookbookController;

class Update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'category_id'      => ['exists:categories,id'],
            'name'             => ['string', 'max:100', "unique:recipes,name,{$this->recipe->id}"],
            'yield_amount'     => ['nullable', 'numeric', 'max:999'],
            'complexity'       => ['string', Rule::in(Recipe::COMPLEXITY_TYPES)],
            'instructions'     => ['string', 'max:16000000'],
            'photos'           => ['nullable', 'array', 'max:20'],
            'photos.*'         => ['nullable', 'image'],
            'preparation_time' => ['nullable', 'string', 'date_format:H:i'],
        ];

        if (CookbookController::isEnabled()) {
            array_merge($rules, ['cookbook_id' => ['nullable', 'exists:cookbooks,id']]);
        }

        if (TagController::isEnabled()) {
            array_merge(
                $rules,
                [
                    'tags'   => ['nullable', 'array'],
                    'tags.*' => ['required_with:tags', 'exists:tags,id']
                ]
            );
        }

        return $rules;
    }
}
