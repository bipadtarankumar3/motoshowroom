<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $currentUser = $this->user();
        if ($currentUser === null) {
            return false;
        }

        $targetUser = $this->route('user');
        $targetUserId = is_object($targetUser) ? $targetUser->id : (int) $targetUser;

        return $currentUser->isAdmin() || $currentUser->id === $targetUserId;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $targetUser = $this->route('user');
        $targetUserId = is_object($targetUser) ? $targetUser->id : (int) $targetUser;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($targetUserId)],
            'phone' => ['nullable', 'string', 'max:30'],
            'bio' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'password' => ['nullable', 'string', 'min:8'],
        ];

        if ($this->user()?->isAdmin()) {
            $rules['role'] = ['required', 'string', 'in:admin,user'];
            $rules['status'] = ['required', 'string', 'in:active,inactive,suspended'];
        }

        return $rules;
    }
}
