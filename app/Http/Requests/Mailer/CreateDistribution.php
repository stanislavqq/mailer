<?php

namespace App\Http\Requests\Mailer;

use Illuminate\Foundation\Http\FormRequest;

class CreateDistribution extends FormRequest
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
            'name' => 'required|string',
            'subject' => 'required|string',
            'email_from' => 'required|string',
            'name_from' => 'required|string',
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'name' => 'Имя',
            'subject' => 'Тема',
            'email_from' => 'Email отправителя',
            'name_from' => 'Имя отправителя'
        ]);
    }
}
