<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTravelRequestStatus extends FormRequest
{
    public function authorize(): bool
    {
        // only admins will be allowed in controller, keep true here if authenticated
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:approved,canceled',
        ];
    }
}
