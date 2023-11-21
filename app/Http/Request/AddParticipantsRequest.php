<?php

namespace App\Http\Request;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\IntegerOrString;

class AddParticipantsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'providers' => ['required', 'array', 'min:1'],
            'providers.*.alias' => ['required', 'string'],
            'providers.*.id' => ['required', new IntegerOrString],
        ];
    }
}
