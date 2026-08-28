<script>
    (function ($) {
        'use strict';

        $(function () {
            $('.solent-material-form').each(function () {
                const $form = $(this);
                const $jobTypes = $form.find('#jobTypes');

                function refreshJobTypes() {
                    if ($.fn.selectpicker && $jobTypes.length) {
                        $jobTypes.selectpicker('refresh');
                    }
                }

                refreshJobTypes();
                $form.on('reset.materialForm', function () {
                    window.setTimeout(refreshJobTypes, 0);
                });
            });
        });
    })(jQuery);
</script>
