<?php

namespace App\Http\Requests;

use App\Models\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() : array
    {
        $rules = [
            'title' => ['string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['string', new Enum(TaskStatus::class)],
            'user_id' => ['nullable'],
        ];

        if($this->isMethodIdempotent()) {
            $rules['title'][] = 'required';
            $rules['description'][] = 'present';
            $rules['status'][] = 'required';
            $rules['user_id'][] = 'present';
        }

        return $rules;
    }
}
