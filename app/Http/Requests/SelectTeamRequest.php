<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SelectTeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Autorisierung erfolgt über Middleware/Gate
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'solver_team_id' => 'required|exists:teams,id',
            'week_label' => 'required|string'
        ];
    }
}
