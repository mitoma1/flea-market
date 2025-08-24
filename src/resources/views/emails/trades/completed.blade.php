@component('mail::message')
# 取引完了のお知らせ

{{ $trade->product->user->name }} 様

あなたの商品「{{ $trade->product->name }}」の取引が完了しました。

購入者: {{ $trade->buyer->name }}
価格: ¥{{ number_format($trade->product->price) }}

@endcomponent