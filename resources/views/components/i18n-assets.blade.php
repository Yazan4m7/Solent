<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="{{ asset('assets/css/rtl.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/site-typography.css') }}" rel="stylesheet">
<script>
    window.SolentI18n = {!! json_encode([
        'locale' => app()->getLocale(),
        'direction' => trans('ui.direction'),
        'messages' => trans('ui.dom'),
        'dataTables' => trans('ui.datatables'),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
</script>
<script src="{{ asset('assets/js/solent-i18n.js') }}" defer></script>
