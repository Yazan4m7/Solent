@php
    $currentLocale = app()->getLocale();
    $targetLocale = $currentLocale === 'ar' ? 'en' : 'ar';
    $switchLabel = $targetLocale === 'ar' ? 'العربية' : 'English';
    $switchUrl = request()->fullUrlWithQuery(['ui_locale' => $targetLocale]);
@endphp
<a href="{{ $switchUrl }}" class="solent-language-switcher {{ $class ?? '' }}" lang="{{ $targetLocale }}"
    dir="{{ $targetLocale === 'ar' ? 'rtl' : 'ltr' }}" hreflang="{{ $targetLocale }}"
    aria-label="{{ $targetLocale === 'ar' ? 'التبديل إلى العربية' : 'Switch to English' }}">
    <i class="fa-solid fa-language" aria-hidden="true"></i>
    <span>{{ $switchLabel }}</span>
</a>
