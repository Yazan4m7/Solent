<div class="stock-form-grid">
    <div class="stock-field stock-span-2">
        <label>Name *</label>
        <input name="name" value="{{ old('name', $item->name ?? '') }}" required placeholder="e.g. Zirconia block A2 98×14">
    </div>
    <div class="stock-field">
        <label>SKU / code</label>
        <input name="sku" value="{{ old('sku', $item->sku ?? '') }}" placeholder="ZIR-A2-9814">
    </div>
    <div class="stock-field">
        <label>Category</label>
        <input name="category" value="{{ old('category', $item->category ?? '') }}" placeholder="Zirconia">
    </div>
    <div class="stock-field">
        <label>Unit *</label>
        <select name="unit" required>
            @foreach(['piece' => 'Piece', 'box' => 'Box', 'pack' => 'Pack', 'gram' => 'Gram', 'kg' => 'Kilogram', 'ml' => 'Milliliter', 'liter' => 'Liter'] as $value => $label)
                <option value="{{ $value }}" @selected(old('unit', $item->unit ?? 'piece') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="stock-field">
        <label>Minimum stock *</label>
        <input type="number" step="0.001" min="0" name="minimum_stock" value="{{ old('minimum_stock', $item->minimum_stock ?? 0) }}" required>
        <small>Low-stock warning level.</small>
    </div>
    <div class="stock-field">
        <label>Target stock</label>
        <input type="number" step="0.001" min="0" name="target_stock" value="{{ old('target_stock', $item->target_stock ?? '') }}">
        <small>Used to calculate suggested purchase quantity.</small>
    </div>
    <div class="stock-field">
        <label>Default unit cost</label>
        <input type="number" step="0.0001" min="0" name="default_unit_cost" value="{{ old('default_unit_cost', $item->default_unit_cost ?? '') }}">
    </div>
    <div class="stock-field stock-span-2">
        <label>Description / specification</label>
        <textarea name="description" rows="3" placeholder="Brand, shade, thickness, size, compatibility, etc.">{{ old('description', $item->description ?? '') }}</textarea>
    </div>
    <div class="stock-field stock-checkbox-field stock-span-2">
        <label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->is_active ?? true))> Active item</label>
    </div>
</div>
