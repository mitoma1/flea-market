<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TradeMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // 認可チェックが不要なら true
    }

    public function rules(): array
    {
        return [
            'body' => 'required|string|max:400',
            'image' => 'nullable|mimes:jpeg,png',
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => '本文を入力してください',
            'body.max'      => '本文は400文字以内で入力してください',
            'image.mimes'   => '.png または .jpeg 形式でアップロードしてください',
        ];
    }
}
