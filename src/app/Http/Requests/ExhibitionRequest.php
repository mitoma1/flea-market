<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'description' => ['required', 'string', 'max:255'],
            'image' => ['required', 'file', 'mimes:jpeg,png'],
            'category_id' => ['required', 'integer'],
            'condition' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }
    public function messages()
    {
        return [
            'name.required' => '商品名を入力してください',
            'description.required' => '商品説明を入力してください',
            'description.max' => '商品説明は255文字以内で入力してください',

            'image.required' => '商品画像を選択してください',
            'image.file' => '有効なファイルをアップロードしてください',
            'image.mimes' => '画像は.jpegまたは.png形式でアップロードしてください',

            'category_id.required' => 'カテゴリーを選択してください',
            'category_id.integer' => 'カテゴリーを正しく選択してください',

            'condition.required' => '商品の状態を選択してください',

            'price.required' => '商品価格を入力してください',
            'price.numeric' => '価格は数値で入力してください',
            'price.min' => '価格は0円以上で入力してください',
        ];
    }
}
