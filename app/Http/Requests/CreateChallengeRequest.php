<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateChallengeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Die eigentliche Autorisierung erfolgt im Controller mit $this->authorize()
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'week_label' => ['required', 'string'],
            'solver_team_id' => ['required', 'exists:teams,id'],
            'challenge_text' => ['required', 'string', 'min:10'],
            'time_limit_minutes' => ['required', 'integer', 'min:5', 'max:60'],
        ];
    }
}
