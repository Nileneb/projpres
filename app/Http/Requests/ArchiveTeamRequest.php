<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ArchiveTeamRequest extends FormRequest
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
            'team_id' => 'sometimes|integer|exists:teams,id',
            'week_label' => 'sometimes|string',
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
            'team_id.integer' => 'Die Team-ID muss eine ganze Zahl sein.',
            'team_id.exists' => 'Das ausgewählte Team existiert nicht.',
            'week_label.string' => 'Die Wochenbezeichnung muss eine Zeichenkette sein.',
        ];
    }
}
