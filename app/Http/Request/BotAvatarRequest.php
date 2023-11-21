<?php

namespace App\Http\Request;

use Illuminate\Foundation\Http\FormRequest;
use App\Facades\Messenger;

class BotAvatarRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $limit = Messenger::getAvatarSizeLimit();
        $mimes = Messenger::getAvatarMimeTypes();

        return [
            'image' => ['required', 'file', "max:$limit", "mimes:$mimes"],
        ];
    }
}
