<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Request;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return optional($this->user())->can('access-project-apis') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        $rules = [
            'name' => ['string', 'min:5', 'max:100']
        ];

        if($this->isMethodIdempotent()) {
            $rules['name'][] = 'required';
        }

        return $rules;
    }
}
