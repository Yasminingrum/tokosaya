<?php
// File: app/Http/Requests/ContactFormRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:180',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:1000',
            'phone' => 'nullable|string|max:15',
            'g-recaptcha-response' => 'nullable|string' // For reCAPTCHA if implemented
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Your name is required',
            'email.required' => 'Email address is required',
            'email.email' => 'Please enter a valid email address',
            'subject.required' => 'Subject is required',
            'message.required' => 'Message is required',
            'message.max' => 'Message cannot exceed 1000 characters'
        ];
    }
}
