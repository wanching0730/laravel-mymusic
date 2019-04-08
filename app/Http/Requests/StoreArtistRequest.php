<?php

namespace App\Http\Requests;

use App\Rules\IsValidGender;
use Illuminate\Foundation\Http\FormRequest;

class StoreArtistRequest extends ApiFormRequest
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
            'name' => 'required|max:50',
            'age' => 'required|integer|min:1|max:100',
            'gender' => ['required', new IsValidGender],
            'nationality' => 'required'
        ];
    }
}
