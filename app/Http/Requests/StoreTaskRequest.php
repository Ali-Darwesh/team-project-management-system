<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTaskRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'due_date' => 'required|date|after_or_equal:today ',
            'user_id' => 'required|integer|exists:users,id',
            'priority' => 'required|in:low,medium,high',
            // 'status' => 'nullable|in:new,in_progress,completed',
            // 'start_time' => 'required|date|before:end_time',
            // 'end_time' => 'required|date|after:start_time'
        ];
    }
    /**
     * The failedValidation method is used to customize the response that is returned when form validation fails 
     * @param Validator $validator
     * it throws an HttpResponseException
     * @return \Illuminate\HTTP\JsonResponse
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'please input data with correct form',
            'errors' => $validator->errors(),
        ]));
    }
    // public function messages()
    // {
    //     return [
    //         'start_time.before' => 'The start time must be before the end time.',
    //         'end_time.after' => 'The end time must be after the start time.',
    //     ];
    // }
}
