@extends('layouts.app')

@section('content')
<div class="address-edit-container">
    <h2 class="title">住所の変更</h2>

    <form action="{{ route('address.update') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" class="form-control"
                value="{{ old('postal_code', $address['postal_code'] ?? '') }}" placeholder="123-4567">
            @error('postal_code')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" class="form-control"
                value="{{ old('address', $address['address'] ?? '') }}">
            @error('address')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" name="building" id="building" class="form-control"
                value="{{ old('building', $address['building'] ?? '') }}">
            @error('building')
            <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-danger">更新する</button>
    </form>
</div>
@endsection