<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
        return [
            'username' => [
                'required', 'string', 'alpha_dash', 'min:3', 'max:24',
                Rule::unique(User::class),
            ],
            'password' => [
                'required', 'string', 'min:6', 'max:72'
            ],
        ];
    }
}
