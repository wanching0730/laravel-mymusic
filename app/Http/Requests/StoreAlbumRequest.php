<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlbumRequest extends ApiFormRequest
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
            'imageUrl' => ['required', 'regex:/(\d)+.(?:jpe?g|png|gif)/']
        ];
    }

    public function messages() {
        return [
            'creationDate.date_format' => 'Date format must be in format like 30-04-10',
            'imageUrl.regex' => 'Image URL format must match valid http or https url and either jpeg,jpg,png,gif format'
        ];
    }
}
