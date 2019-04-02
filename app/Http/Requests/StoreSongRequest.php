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
            //'creationDate' => 'date_format:Y-m-d|after:yesterday',
            'creationDate' => 'date_format:Y-m-d',
        ];
    }

    public function messages() {
        return [
            'creationDate.date_format' => 'Date format must be in format like 30-04-10'
        ];
    }
}
