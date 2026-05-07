@php
    $logo = \App\Models\Setting::get('app_logo');
@endphp
<img src="{{ $logo ? asset('storage/' . $logo) : asset('img/koarmada.png') }}" alt="Logo" {{ $attributes->class('h-50 w-auto object-contain') }}>
