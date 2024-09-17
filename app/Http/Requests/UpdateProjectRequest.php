<?php

namespace App\Http\Requests;

use App\Models\Project;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        $project = Project::findOrFail($this->project->id);
        // Check if the user is associated with the project
        $userProjectRelation = $project->users()->where('user_id', $user->id)->first()?->pivot->role;

        if (!$user->is_admin && !$userProjectRelation) {
            abort(response()->json([
                'message' => 'User is not associated with this project.',
            ], 404));
        }
        // Check if the user is a manager for this project
        $isManager = $userProjectRelation === 'manager';
        // Ensure that there is an authenticated user
        if (!$user || (!$user->is_admin && !$isManager)) {
            abort(response()->json([
                'error' => 'You are not authorized to perform this action.',
            ], 403));
        }

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
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:255',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'please input data with correct form',
            'errors' => $validator->errors(),
        ]));
    }
}
