<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class GenerateTeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('manage-teams');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'week_label' => 'required|string',
            'team_size' => 'required|integer|min:2|max:10',
            'force' => 'sometimes|boolean',
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
            'week_label.required' => 'Bitte gib eine Wochenbezeichnung an.',
            'team_size.required' => 'Bitte gib die Teamgröße an.',
            'team_size.integer' => 'Die Teamgröße muss eine ganze Zahl sein.',
            'team_size.min' => 'Die Teamgröße muss mindestens 2 sein.',
            'team_size.max' => 'Die Teamgröße darf maximal 10 sein.',
        ];
    }
}
