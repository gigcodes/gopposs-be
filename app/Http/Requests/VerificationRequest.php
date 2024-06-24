<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerificationRequest extends FormRequest
{
    public function rules()
    {
        return [
            'token' => 'required|string|size:' . config('verification.token_length', 5),
        ];
    }

    public function authorize()
    {
        return true;
    }
}
