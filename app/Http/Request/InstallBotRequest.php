<?php

namespace App\Http\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Facades\MessengerBots;

class InstallBotRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'alias' => ['required', Rule::in(MessengerBots::getPackagedBotAliases())],
        ];
    }
}
