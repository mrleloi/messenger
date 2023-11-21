<?php

namespace App\Http\Request;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\IntegerOrString;

class FriendRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'recipient_id' => ['required', new IntegerOrString],
            'recipient_alias' => ['required', 'string'],
        ];
    }
}
