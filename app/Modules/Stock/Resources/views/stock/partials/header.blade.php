<div class="stock-shell">
    <div class="stock-topbar">
        <div>
            <div class="stock-eyebrow">SOLENT</div>
            <h1 class="stock-title">Stock</h1>
        </div>
        <div class="stock-top-actions">
            <a class="stock-btn stock-btn-primary" href="{{ route('stock.purchases.create') }}">+ Receive purchase</a>
            <a class="stock-btn" href="{{ route('stock.adjustments.create') }}">Adjust stock</a>
        </div>
    </div>

    <nav class="stock-nav" aria-label="Stock navigation">
        <a class="{{ request()->routeIs('stock.index') ? 'active' : '' }}" href="{{ route('stock.index') }}">Overview</a>
        <a class="{{ request()->routeIs('stock.items.*') ? 'active' : '' }}" href="{{ route('stock.items.index') }}">Items</a>
        <a class="{{ request()->routeIs('stock.needs') ? 'active' : '' }}" href="{{ route('stock.needs') }}">Need to buy</a>
        <a class="{{ request()->routeIs('stock.purchases.*') ? 'active' : '' }}" href="{{ route('stock.purchases.index') }}">Purchases</a>
        <a class="{{ request()->routeIs('stock.movements.*') || request()->routeIs('stock.adjustments.*') ? 'active' : '' }}" href="{{ route('stock.movements.index') }}">Movements</a>
        <a class="{{ request()->routeIs('stock.suppliers.*') ? 'active' : '' }}" href="{{ route('stock.suppliers.index') }}">Suppliers</a>
        <a class="{{ request()->routeIs('stock.locations.*') ? 'active' : '' }}" href="{{ route('stock.locations.index') }}">Locations</a>
    </nav>

    @include('stock.partials.messages')
