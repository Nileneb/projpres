<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitMatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Get the Matches model instance directly from the controller parameter
        $match = request()->route()->parameter('match');
        
        // Prüfen, ob der Status 'in_progress' ist
        if ($match && $match->status !== 'in_progress') {
            return false;
        }
        
        // Weitere Autorisierungslogik wird über die Policy im Controller gehandhabt
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
            'submission_url' => ['required', 'url'],
        ];
    }
}
