<?php

namespace App\Http\Requests\Mailer;

class UpdateContact extends CreateContact
{
    public function messages()
    {
        return parent::messages();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return parent::rules();
    }
}
