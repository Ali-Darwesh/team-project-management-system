<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'nullable|in:new,in_progress,completed',
            'priority' => 'nullable|in:low,medium,high',
        ];
    }
    public function messages()
    {
        return [
            'status.in' => 'The selected status must be one of(new,in_progress,completed).',
            'priority.in' => 'The selected priority must be one of(low,medium,high).',
        ];
    }
}
