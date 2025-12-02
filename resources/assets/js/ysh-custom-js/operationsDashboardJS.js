// Existing code remains the same, adding/modifying tab initialization



// Ensure tabs are initialized on page load
$(document).ready(function() {
    // Automatically select the first tab on page load
    $('.stageSidebar button[role="tab"]').first().trigger('click');

    // Ensure Macaw Tabs is initialized
    if (typeof MacawTabs !== 'undefined') {
        MacawTabs.init();
    }
});
