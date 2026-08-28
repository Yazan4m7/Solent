<div class="permissions-box" id="Permission" aria-label="Permissions">
    @foreach($permissions as $perm)
        @php
            $permissionId = (string) $perm->id;
            $permissionInputId = $permissionInputPrefix . '-permission-' . $permissionId;
        @endphp
        <label class="permission-item" for="{{ $permissionInputId }}">
            <input
                type="checkbox"
                class="permission-checkbox"
                id="{{ $permissionInputId }}"
                name="permission[]"
                value="{{ $perm->id }}"
                {{ in_array($permissionId, $selectedPermissionIds, true) ? 'checked' : '' }}
            >
            <span class="permission-icon permission-icon-off" aria-hidden="true"><i class="fa fa-times"></i></span>
            <span class="permission-icon permission-icon-on" aria-hidden="true"><i class="fa fa-check"></i></span>
            <span class="permission-name">{{ $perm->name }}</span>
        </label>
    @endforeach
</div>
