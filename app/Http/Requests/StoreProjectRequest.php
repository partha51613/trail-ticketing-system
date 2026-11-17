<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Project::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'min:2', 'max:6', 'alpha_num', 'unique:projects,code'],
            'description' => ['nullable', 'string'],
            'manager_ids' => ['nullable', 'array'],
            'manager_ids.*' => ['nullable', 'exists:users,id'],
        ];
    }
}
