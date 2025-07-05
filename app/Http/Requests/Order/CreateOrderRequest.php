<?php
// File: app/Http/Requests/Order/CreateOrderRequest.php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'notes' => 'nullable|string|max:500',
            'terms_accepted' => 'required|accepted'
        ];
    }

    public function messages()
    {
        return [
            'terms_accepted.accepted' => 'You must accept the terms and conditions to place your order'
        ];
    }
}
