<?php

namespace App\Http\Requests;

use App\Models\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() : bool
    {
        return optional($this->user())->can('access-user-apis') ?? false;
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
            'role' => [
                'required', 'string', new Enum(UserRole::class),
            ],
            'password' => [
                'required', 'string', 'min:6', 'max:72'
            ],
        ];
    }
}
