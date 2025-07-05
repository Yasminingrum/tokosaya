<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
        $userId = Auth::id();

        return [
            'first_name' => [
                'required',
                'string',
                'max:50',
                'min:2',
                'regex:/^[a-zA-Z\s]+$/' // Only letters and spaces
            ],
            'last_name' => [
                'required',
                'string',
                'max:50',
                'min:2',
                'regex:/^[a-zA-Z\s]+$/' // Only letters and spaces
            ],
            'email' => [
                'required',
                'email:rfc,dns',
                'max:180',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'phone' => [
                'nullable',
                'string',
                'max:15',
                'min:10',
                'regex:/^[\+]?[0-9\-\(\)\s]+$/', // Phone number format
                Rule::unique('users', 'phone')->ignore($userId)
            ],
            'date_of_birth' => [
                'nullable',
                'date',
                'before:today',
                'after:1900-01-01'
            ],
            'gender' => [
                'nullable',
                'in:M,F,O'
            ],
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048', // 2MB max
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000'
            ]
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'first_name.min' => 'First name must be at least 2 characters.',
            'first_name.max' => 'First name cannot exceed 50 characters.',
            'first_name.regex' => 'First name can only contain letters and spaces.',

            'last_name.required' => 'Last name is required.',
            'last_name.min' => 'Last name must be at least 2 characters.',
            'last_name.max' => 'Last name cannot exceed 50 characters.',
            'last_name.regex' => 'Last name can only contain letters and spaces.',

            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'email.max' => 'Email address cannot exceed 180 characters.',

            'phone.min' => 'Phone number must be at least 10 digits.',
            'phone.max' => 'Phone number cannot exceed 15 characters.',
            'phone.regex' => 'Please provide a valid phone number.',
            'phone.unique' => 'This phone number is already registered.',

            'date_of_birth.date' => 'Please provide a valid birth date.',
            'date_of_birth.before' => 'Birth date must be before today.',
            'date_of_birth.after' => 'Birth date must be after 1900.',

            'gender.in' => 'Please select a valid gender option.',

            'avatar.image' => 'Avatar must be an image file.',
            'avatar.mimes' => 'Avatar must be in JPG, JPEG, PNG, or WebP format.',
            'avatar.max' => 'Avatar file size cannot exceed 2MB.',
            'avatar.dimensions' => 'Avatar must be between 100x100 and 2000x2000 pixels.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'email' => 'email address',
            'phone' => 'phone number',
            'date_of_birth' => 'birth date',
            'gender' => 'gender',
            'avatar' => 'profile picture'
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
            // Custom validation: Check if birth date makes user at least 13 years old
            if ($this->date_of_birth) {
                $birthDate = \Carbon\Carbon::parse($this->date_of_birth);
                $age = $birthDate->diffInYears(\Carbon\Carbon::now());

                if ($age < 13) {
                    $validator->errors()->add(
                        'date_of_birth',
                        'You must be at least 13 years old to use this service.'
                    );
                }
            }

            // Custom validation: Phone number format check for Indonesian numbers
            if ($this->phone) {
                $phone = preg_replace('/[^0-9+]/', '', $this->phone);

                // Check Indonesian phone number patterns
                if (!preg_match('/^(\+62|62|0)[0-9]{8,12}$/', $phone)) {
                    $validator->errors()->add(
                        'phone',
                        'Please provide a valid Indonesian phone number.'
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
        // Trim and clean text fields
        $this->merge([
            'first_name' => $this->first_name ? trim($this->first_name) : null,
            'last_name' => $this->last_name ? trim($this->last_name) : null,
            'email' => $this->email ? strtolower(trim($this->email)) : null,
            'phone' => $this->phone ? trim($this->phone) : null
        ]);

        // Convert empty strings to null
        foreach (['first_name', 'last_name', 'email', 'phone', 'date_of_birth', 'gender'] as $field) {
            if ($this->$field === '') {
                $this->merge([$field => null]);
            }
        }
    }
}
