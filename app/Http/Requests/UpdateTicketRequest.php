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
        $ticket = $this->route('ticket');

        if ($ticket instanceof Ticket) {
            return $this->user()?->can('update', $ticket) ?? false;
        }

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
            'project_id' => [
                'required',
                'integer',
                Rule::exists('projects', 'id')->whereNull('deleted_at')->where(fn ($q) => $q->where('status', '!=', 'closed')),
            ],
            'priority' => ['required', 'string', Rule::in(['low', 'medium', 'high', 'urgent'])],
            'due_date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'assigned_to_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->whereNull('deleted_at'),
            ],
            'attachments' => ['nullable', 'array', 'max:10'],
            'attachments.*' => ['file', 'mimes:png,jpg,jpeg,webp,gif,pdf,doc,docx,txt,csv,log,zip', 'max:51200'],
            'remove_attachments' => ['nullable', 'array'],
            'remove_attachments.*' => ['string'],
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
