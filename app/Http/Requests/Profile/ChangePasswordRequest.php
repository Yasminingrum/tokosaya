<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                'string'
            ],
            'new_password' => [
                'required',
                'string',
                'different:current_password',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
            'new_password_confirmation' => [
                'required',
                'string'
            ]
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'current_password.required' => 'Please enter your current password.',

            'new_password.required' => 'Please enter a new password.',
            'new_password.different' => 'New password must be different from current password.',
            'new_password.confirmed' => 'New password confirmation does not match.',
            'new_password.min' => 'New password must be at least 8 characters long.',

            'new_password_confirmation.required' => 'Please confirm your new password.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'current_password' => 'current password',
            'new_password' => 'new password',
            'new_password_confirmation' => 'password confirmation'
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($this->expectsJson()) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Custom validation: Verify current password
            if ($this->current_password) {
                $user = Auth::user();
                if (!\Illuminate\Support\Facades\Hash::check($this->current_password, $user->password_hash)) {
                    $validator->errors()->add(
                        'current_password',
                        'The current password you entered is incorrect.'
                    );
                }
            }

            // Custom validation: Check if new password is commonly used
            if ($this->new_password) {
                $commonPasswords = [
                    'password', '12345678', 'qwerty123', 'admin123',
                    'password123', '123456789', 'welcome123'
                ];

                if (in_array(strtolower($this->new_password), $commonPasswords)) {
                    $validator->errors()->add(
                        'new_password',
                        'This password is too common. Please choose a more secure password.'
                    );
                }
            }

            // Custom validation: Check password strength scoring
            if ($this->new_password) {
                $score = $this->calculatePasswordStrength($this->new_password);
                if ($score < 60) { // Minimum score for acceptable password
                    $validator->errors()->add(
                        'new_password',
                        'Password is too weak. Please include a mix of uppercase, lowercase, numbers, and symbols.'
                    );
                }
            }
        });
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // No trimming for passwords to preserve exact input
    }

    /**
     * Calculate password strength score
     */
    private function calculatePasswordStrength(string $password): int
    {
        $score = 0;
        $length = strlen($password);

        // Length scoring
        if ($length >= 8) $score += 25;
        if ($length >= 12) $score += 15;
        if ($length >= 16) $score += 10;

        // Character variety scoring
        if (preg_match('/[a-z]/', $password)) $score += 10;
        if (preg_match('/[A-Z]/', $password)) $score += 10;
        if (preg_match('/[0-9]/', $password)) $score += 10;
        if (preg_match('/[^a-zA-Z0-9]/', $password)) $score += 15;

        // Pattern penalties
        if (preg_match('/(.)\1{2,}/', $password)) $score -= 10; // Repeated characters
        if (preg_match('/123|abc|qwe/i', $password)) $score -= 15; // Sequential patterns

        return max(0, min(100, $score));
    }
}
