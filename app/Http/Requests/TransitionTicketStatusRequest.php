<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransitionTicketStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */


    public function messages(): array
    {
        return [
            'comment.required_if' => 'A comment explaining the reason is required when marking a ticket as blocked.',
        ];
    }


    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in(['new', 'in_progress', 'waiting', 'blocked', 'closed']),
            ],
            'comment' => ['required_if:status,blocked', 'nullable', 'string'],
        ];
    }
}
