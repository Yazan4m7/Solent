<script>
    (function ($) {
        'use strict';

        function initializeUserAccessControls() {
            $('.user-management-form').each(function () {
                const $form = $(this);
                const $adminToggle = $form.find('#is_admin');
                const $permissionBox = $form.find('#Permission');
                const $permissionInputs = $permissionBox.find('.permission-checkbox');
                const $deliveryDriverSection = $form.find('.delivery-driver-section');

                function syncDeliveryDriverSection() {
                    const isAdmin = $adminToggle.is(':checked');
                    const hasDeliveryPermission = $permissionBox
                        .find('.permission-checkbox[value="131"]')
                        .is(':checked');

                    $deliveryDriverSection.toggle(!isAdmin && hasDeliveryPermission);
                }

                function syncPermissionState() {
                    const isAdmin = $adminToggle.is(':checked');

                    $permissionInputs.prop('disabled', isAdmin);
                    $permissionBox
                        .toggleClass('is-disabled', isAdmin)
                        .attr('aria-disabled', isAdmin ? 'true' : 'false');
                    $permissionBox.find('.permission-item').toggleClass('is-disabled', isAdmin);
                    syncDeliveryDriverSection();
                }

                $adminToggle.on('change.userAccess', syncPermissionState);
                $permissionInputs.on('change.userAccess', syncDeliveryDriverSection);
                $form.on('reset.userAccess', function () {
                    window.setTimeout(syncPermissionState, 0);
                });

                syncPermissionState();
            });
        }

        $(initializeUserAccessControls);
    })(jQuery);
</script>
