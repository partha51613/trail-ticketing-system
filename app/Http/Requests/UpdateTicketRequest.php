<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'priority' => ['required', 'string', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'due_date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'assigned_to_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    /**
     * Configure the validator instance
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $ticket = $this->route('ticket');

            if ($ticket instanceof Ticket) {
                $newProjectId = (int) $this->input('project_id');

                // If project_id is being changed, verify transfer permission

                if ($newProjectId && $newProjectId !== $ticket->project_id) {
                    if (! $this->user()?->can('transferProject', [$ticket, $newProjectId])) {
                        $validator->errors()->add(
                            'project_id',
                            'You are not authorized to transfer this ticket to another project'
                        );
                    }
                }
            }
        });
    }
}
