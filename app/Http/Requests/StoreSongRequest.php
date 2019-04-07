<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSongRequest extends ApiFormRequest
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
            'creationDate' => 'date_format:Y-m-d',
            'duration' => 'integer|min:10'
        ];
    }

    public function messages() {
        return [
            'creationDate.date_format' => 'Date format must be in format like 2018-04-30'
        ];
    }
}
