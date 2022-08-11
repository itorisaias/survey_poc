<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSurveyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'description' => 'required|string',
            'question' => 'required|string',
            'question_type' => 'string|in:likert,yes_no',
            'customers' => 'required|array',
            'customers.*' => 'required|integer|exists:customers,id',
        ];
    }
}
