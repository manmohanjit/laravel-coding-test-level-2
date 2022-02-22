<?php

namespace App\Http\Requests;

use App\Models\Enums\TaskStatus;
use Illuminate\Auth\Access\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
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
        $user = $this->user();

        if(!$user) {
            return false;
        }

        // Team member can only change the status of the task assigned to them, they can edit any other attribute in a task.
        return $user->can('access-project-apis') || $user->can('update-task-status', $this->route('task'));
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
            'status' => ['string', new Enum(TaskStatus::class)], // Task will have either NOT_STARTED, IN_PROGRESS, READY_FOR_TEST, COMPLETED status
        ];

        if($this->isMethodIdempotent()) {
            $rules['title'][] = 'required';
            $rules['description'][] = 'present';
            $rules['status'][] = 'required';
        }

        // Only PRODUCT_OWNER role can assign tasks to a team member in their project
        if($this->user()->can('access-project-apis')) {
            $rules['user_id'] = [
                'nullable',
                Rule::exists('project_user', 'user_id')
                    ->where('project_id',  $this->project->id)
            ];

            if($this->isMethodIdempotent()) {
                $rules['user_id'][] = 'present';
            }
        }

        return $rules;
    }
}
