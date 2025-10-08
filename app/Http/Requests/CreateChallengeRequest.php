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
        // Autorisierung erfolgt im Controller mit komplexen Checks
        return true;
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
    
    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'challenge_text.min' => 'Die Challenge-Beschreibung muss mindestens 10 Zeichen lang sein.',
            'time_limit_minutes.min' => 'Die Zeitbegrenzung muss mindestens 5 Minuten betragen.',
            'time_limit_minutes.max' => 'Die Zeitbegrenzung darf maximal 60 Minuten betragen.',
        ];
    }
}
