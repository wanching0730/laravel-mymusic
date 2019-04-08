<?php

namespace App\Http\Requests;

use App\Rules\IsValidRole;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends ApiFormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'max:50',
            'email' => 'required|email',
            'role' => ['required', new IsValidRole]
        ];
    }
}
