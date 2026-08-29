<div class="card mb-3">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-1">{{ __('financing::financing.financing') }}</h5>
            <small class="text-muted">{{ __('financing::financing.module_description') }}</small>
        </div>
        <form method="POST" action="{{ route('admin.settings.module.update') }}">
            @csrf
            <input type="hidden" name="module" value="financing">
            <input type="hidden" name="enabled" value="{{ (string) setting('module_financing','0') === '1' ? 0 : 1 }}">
            <button class="btn btn-{{ (string) setting('module_financing','0') === '1' ? 'success' : 'secondary' }}">
                {{ (string) setting('module_financing','0') === '1' ? __('financing::financing.enabled') : __('financing::financing.disabled') }}
            </button>
        </form>
    </div>
</div>
