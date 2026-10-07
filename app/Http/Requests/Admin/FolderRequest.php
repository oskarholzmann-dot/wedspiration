<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FolderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // The admin middleware on the route group already checks this
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // The owner is only chosen when creating a folder
            'user_id' => $this->isMethod('post') ? ['required', 'exists:users,id'] : ['prohibited'],
            'name' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'wedding_date' => ['nullable', 'date'],
        ];
    }

    /**
     * Get custom names for the fields in error messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ['user_id' => 'owner'];
    }
}
