<?php

namespace App\Http\Requests;

use App\Models\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Symfony\Component\HttpFoundation\Request;

class UpdateUserRequest extends FormRequest
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
        $rules = [
            'username' => [
                'string', 'alpha_dash', 'min:3', 'max:24',
                Rule::unique(User::class)->ignoreModel($this->route('user')),
            ],
            'role' => [
                'string', new Enum(UserRole::class),
            ],
            'password' => [
                'string', 'min:6', 'max:72'
            ],
        ];

        if($this->isMethodIdempotent()) {
            $rules['username'][] = 'required';
            $rules['role'][] = 'required';
            $rules['password'][] = 'required';
        }

        return $rules;
    }
}
