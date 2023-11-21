<?php

namespace App\Http\Request;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Facades\Messenger;

class ThreadSettingsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'min:2', 'max:255'],
            'add_participants' => ['required', 'boolean'],
            'messaging' => ['required', 'boolean'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  Validator  $validator
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        $validator->sometimes('knocks', [
            'required',
            'boolean',
        ], fn () => Messenger::isKnockKnockEnabled());

        $validator->sometimes('calling', [
            'required',
            'boolean',
        ], fn () => Messenger::isCallingEnabled());

        $validator->sometimes('invitations', [
            'required',
            'boolean',
        ], fn () => Messenger::isThreadInvitesEnabled());

        $validator->sometimes('chat_bots', [
            'required',
            'boolean',
        ], fn () => Messenger::isBotsEnabled());
    }
}
