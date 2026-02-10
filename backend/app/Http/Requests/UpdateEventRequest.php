<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): true
    {
        return true;
    }

    /**
     * Szabályok
     *
     * @return string[]
     */
    public function rules(): array
    {
        // Csak description frissíthető
        return [
            'description' => 'nullable|string',
        ];
    }
}
