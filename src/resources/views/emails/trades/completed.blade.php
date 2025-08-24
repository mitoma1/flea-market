@component('mail::message')
# 取引完了のお知らせ

{{ $trade->product->name }} の取引が完了しました。

@component('mail::button', ['url' => route('trades.show', $trade->id)])
取引画面を見る
@endcomponent

今後ともよろしくお願いいたします。

@endcomponent