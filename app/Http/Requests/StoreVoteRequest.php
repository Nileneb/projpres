<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVoteRequest extends FormRequest
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
        // Primär score validieren, aber auch rating als Fallback akzeptieren
        return [
            'score' => ['required_without:rating', 'integer', 'min:1', 'max:5'],
            'rating' => ['required_without:score', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string'],
        ];
    }

    /**
     * Get the validated data from the request.
     *
     * @return array
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        // Einheitliches Handling für score - bevorzuge score wenn vorhanden
        if (!isset($validated['score']) && isset($validated['rating'])) {
            $validated['score'] = $validated['rating'];
        }

        // rating entfernen, wenn es zusätzlich zu score existiert
        if (isset($validated['rating'])) {
            unset($validated['rating']);
        }

        return $validated;
    }
}
