@php
    $quickNavUser = Auth::user();
    $quickNavPermissions = Cache::get('user' . $quickNavUser->id);
    $quickNavRoute = Route::currentRouteName();
    $quickNavUi = trans('ui.dom');
    $canUseQuickNav = static function (int $permissionId) use ($quickNavPermissions, $quickNavUser): bool {
        return (bool) ($quickNavUser->is_admin ?? false)
            || ($quickNavPermissions && $quickNavPermissions->contains('permission_id', $permissionId));
    };
    $quickNavItems = array_values(array_filter([
        $canUseQuickNav(123) ? [
            'route' => 'home',
            'label' => $quickNavUi['Home'] ?? 'Home',
            'icon' => 'home',
            'active' => $quickNavRoute === 'home',
        ] : null,
        $canUseQuickNav(106) ? [
            'route' => 'admin-dashboard-v2',
            'label' => $quickNavUi['Operations'] ?? 'Operations',
            'icon' => 'dashboard',
            'active' => $quickNavRoute === 'admin-dashboard-v2',
        ] : null,
        $canUseQuickNav(103) ? [
            'route' => 'cases-index',
            'label' => $quickNavUi['Cases'] ?? 'Cases',
            'icon' => 'case',
            'active' => in_array($quickNavRoute, ['cases-index', 'new-case-view'], true),
        ] : null,
        $canUseQuickNav(113) ? [
            'route' => 'view-cases-monitor',
            'label' => $quickNavUi['Live monitor'] ?? 'Live monitor',
            'icon' => 'monitor',
            'active' => $quickNavRoute === 'view-cases-monitor',
        ] : null,
        $canUseQuickNav(109) ? [
            'route' => 'delivery-schedule',
            'label' => $quickNavUi['Deliveries'] ?? 'Deliveries',
            'icon' => 'clock',
            'active' => $quickNavRoute === 'delivery-schedule',
        ] : null,
    ]));
    $quickNavVariant = $quickNavVariant ?? 'desktop';
@endphp

@if (count($quickNavItems) > 0)
    <nav class="solent-quick-nav solent-quick-nav--{{ $quickNavVariant }}"
        aria-label="{{ $quickNavUi['Quick navigation'] ?? 'Quick navigation' }}">
        @foreach ($quickNavItems as $quickNavItem)
            <a class="solent-quick-nav__item{{ $quickNavItem['active'] ? ' is-active' : '' }}"
                href="{{ route($quickNavItem['route']) }}"
                @if ($quickNavItem['active']) aria-current="page" @endif>
                <span class="solent-quick-nav__icon">
                    @include('layouts.navbars.partials.sidebar-icon', ['name' => $quickNavItem['icon']])
                </span>
                <span class="solent-quick-nav__label">{{ $quickNavItem['label'] }}</span>
            </a>
        @endforeach
    </nav>
@endif
