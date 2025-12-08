



let deviceSelected = 0;
let selectedCases = [];
let currentModalId = 0;
let caseIDFromOldDialog = 0;


function setInnerTab(btnElement) {
    let id = btnElement.id;
    if (id.toLowerCase().includes('3dprinting')) {
        id = id.replace(/3[dD][pP]rinting/i, '3dprinting');
    }
    Cookies.set('inner' + $(btnElement).attr('href'), id);
    console.log("set cookie for : " + 'inner' + $(btnElement).attr('href') + ' => ' + id);

    const stageKey = $(btnElement).data('stageid');
    console.log("stageKey:", stageKey);

    // Hide all inner tab panels for this stage
    console.log("Hiding: #active-" + stageKey + ", #waiting-" + stageKey);
    $(`#active-${stageKey}, #waiting-${stageKey}`).attr('hidden', true).removeClass('active');

    // Show the selected panel
    const panelId = $(btnElement).attr('aria-controls');
    console.log("Showing panel:", panelId);
    $(`#${panelId}`).removeAttr('hidden').addClass('active');

    // Update tab button states
    const tablist = $(btnElement).closest('[role="tablist"]');
    tablist.find('[role="tab"]').attr('aria-selected', false).attr('tabindex', -1);
    $(btnElement).attr('aria-selected', true).removeAttr('tabindex');
}

function setOuterTab(btnElement) {
    let key = btnElement.id;
    console.log("setting outer tab, key received :  " + key);
    // Always use lowercase for 3dprinting
    if (key.toLowerCase().includes('3dprinting')) {
        key = "3dprinting";
        console.log("in if lower case " + 'activeOuterTab' + ' => ' + key);
        key = "3dprinting";
    }
    Cookies.set('activeOuterTab', key);
    console.log("set outer cookie for : " + 'activeOuterTab' + ' =>' + key);
}
function YSH_openSlidePanel(caseId) {
    console.log('Opening slide panel for case ID:', caseId);
    const overlay = document.getElementById('YSH-slide-overlay-' + caseId);

    if (overlay) {
        console.log('Found overlay element:', overlay);
        // Force display to flex and add active class immediately
        overlay.style.display = 'flex';
        overlay.classList.add('YSH-active');
        document.body.classList.add('YSH-no-scroll'); // Prevent background scrolling

        // Add subtle entrance animation for panel content
        const panel = overlay.querySelector('.YSH-slide-panel');
        if (panel) {
            console.log('Found panel element:', panel);
            panel.classList.add('YSH-panel-animate');

            // Remove animation class after animation completes
            setTimeout(() => {
                panel.classList.remove('YSH-panel-animate');
            }, 500);
        } else {
            console.error('Panel element not found within overlay');
        }
    } else {
        console.error('Slide overlay not found for case ID:', caseId);
        // Try to find the overlay using a more flexible selector
        const overlays = document.querySelectorAll('[id^="YSH-slide-overlay-"]');
        console.log('Available overlays:', overlays.length);
        if (overlays.length > 0) {
            // If we have any overlays, open the first one as a fallback
            const firstOverlay = overlays[0];
            console.log('Using first available overlay as fallback:', firstOverlay);
            firstOverlay.style.display = 'flex';
            firstOverlay.classList.add('YSH-active');
            document.body.classList.add('YSH-no-scroll');
        }
    }
}

function YSH_closeSlidePanel(caseId) {
    console.log('Closing slide panel for case ID:', caseId);
    const overlay = document.getElementById('YSH-slide-overlay-' + caseId);
    if (overlay) {
        console.log('Found overlay element to close:', overlay);
        // Add closing animation
        overlay.classList.add('YSH-closing');

        // Add subtle exit animation for panel content
        const panel = overlay.querySelector('.YSH-slide-panel');
        if (panel) {
            panel.classList.add('YSH-panel-exit');
        }

        overlay.addEventListener('animationend', () => {
            document.body.classList.remove('YSH-no-scroll'); // Re-enable scrolling
            overlay.classList.remove('YSH-active', 'YSH-closing');
            if (panel) {
                panel.classList.remove('YSH-panel-exit');
            }
            overlay.style.display = 'none'; // Hide the overlay after animation
        }, {
            once: true
        });
    } else {
        console.error('Slide overlay not found for case ID:', caseId);
        // Try to find and close any active overlays
        const activeOverlays = document.querySelectorAll('.YSH-active[id^="YSH-slide-overlay-"]');
        console.log('Found active overlays:', activeOverlays.length);
        activeOverlays.forEach(activeOverlay => {
            activeOverlay.classList.add('YSH-closing');

            // Add subtle exit animation for panel content
            const panel = activeOverlay.querySelector('.YSH-slide-panel');
            if (panel) {
                panel.classList.add('YSH-panel-exit');
            }

            activeOverlay.addEventListener('animationend', () => {
                document.body.classList.remove('YSH-no-scroll');
                activeOverlay.classList.remove('YSH-active', 'YSH-closing');
                if (panel) {
                    panel.classList.remove('YSH-panel-exit');
                }
                activeOverlay.style.display = 'none'; // Hide the overlay after animation
            }, {
                once: true
            });
        });
    }
}

// Function to handle radio button changes
function buildRadioChange(radio, type, deviceId) {
    type = CSS.escape(type);

    // Enable the button when a radio is selected
    // document.querySelector(`.${type}.blackbox-button-outer.device-${deviceId}`).classList.remove('disabled');
    document.querySelector(`.${type}.blackbox-button-outer.device-${deviceId}`).classList.add('enabled');

    // document.querySelector(`.${type}.blackbox-button-inner.device-${deviceId}`).classList.remove('disabled');
    document.querySelector(`.${type}.blackbox-button-inner.device-${deviceId}`).classList.add('enabled');
}


function toggleButtonState() {
    const checkboxes = document.querySelectorAll('.active-checkbox');
    const buttons = document.querySelectorAll('.active-cases-btn');

    // Check if any checkbox is checked
    const isChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);

    buttons.forEach(button => {
        const components = button.children; // Assuming button has 3 components inside

        for (let component of components) {
            if (isChecked) {
                component.classList.add('enabled');
                component.classList.remove('disabled');
            } else {
                component.classList.add('disabled');
                component.classList.remove('enabled');
            }
        }
    });
}

function getCheckedValues(tableKey) {
    var checkedValues = [];
    if (caseIDFromOldDialog != 0) return [caseIDFromOldDialog];
    console.log("Checking for checkboxes with selector: " + 'input[name="CheckBoxes' + tableKey + '[]"]:checked');
    // Select checkboxes with the specific dynamic name
    $('input[name="CheckBoxes' + tableKey + '[]"]:checked').each(function () {
        checkedValues.push($(this).val());
    });
    if (checkedValues.length == 0) {
        console.log("No checkboxes checked with selector: " + 'input[name="CheckBoxes' + tableKey + '[]"]:checked');
        console.log("Trying alternative selector with formatted id: " + 'input[name="CheckBoxes' + formatId(
            tableKey) + '[]"]:checked');
        $('input[name="CheckBoxes' + formatId(tableKey) + '[]"]:checked').each(function () {
            checkedValues.push($(this).val());
        });
    }
    console.log("Checked Count for", tableKey, ":", checkedValues.length); // Debugging
    console.log("Checked Values:", checkedValues); // Debugging

    return checkedValues;
}

function submitForm(key, action, deviceId = 0) {
    if (!deviceSelected) //if  0 (false)  = >  true => assign
        deviceSelected = deviceId;


    console.log("submitting  " + key + " action" + action + " deviceId" + deviceId);
    var checkedValues = getCheckedValues(key); // Get checked values for the correct table
    var form = document.getElementById("hiddenForm" + key);

    console.log("check boxes selector: " + "CheckBoxes" + key);
    // Select the correct hidden inputs based on the form's key
    try {
        document.getElementById("WaitingPopupCheckBoxes" + key).value = checkedValues;
    } catch (e) {
        try {
            key = formatId(key);
            document.getElementById("WaitingPopupCheckBoxes" + key).value = checkedValues;
        } catch (e2) {
            console.error("Both elements not found:", e, e2);
        }
    }

    console.log("WaitingPopupCheckBoxes : " + document.getElementById("WaitingPopupCheckBoxes" + key).value);
    if (action == 4)
        document.getElementById("hidden3dprintingBuildName").value = document.getElementById("silicon-valley-input")
            .value;

    document.getElementById("deviceId-" + key).value = deviceSelected
    console.log(checkedValues.length + " === caseIDFromOldDialog:" + caseIDFromOldDialog + "    =xx== " +
        deviceSelected); // Debugging log

    form.action =
        action == 0 ? routes.setMultiple :
            action == 1 ? routes.activateMultiple :
                action == 2 ? routes.finishMultiple :
                    action == 3 ? routes.assignDelivery :
                        action == 4 ? "/set-cases-on-printer" :
                            action == 5 ? routes.activate3D :
                                action == 6 ? routes.finish3D :
                                    routes.finishMultiple;


    console.log("form action: " + form.action);

    document.getElementById("hiddenForm" + key).submit(); // Submit the correct form

}


function formatId(id) {
    // Normalize for 3D printing first
    if (id && id.toLowerCase().includes('3dprinting')) {
        return "3dprinting";
    }
    // Check if id (in lowercase) starts with "3d"
    else if (id && id.toLowerCase().startsWith("3d")) {
        // Preserve "3D" then capitalize the first character of the rest of the string.
        return "3D" + id.slice(2).charAt(0).toUpperCase() + id.slice(3);
    } else {
        // Default: Capitalize the first character.
        return id.charAt(0).toUpperCase() + id.slice(1);
    }
}


function casesDialogCheckBoxChange(stage, deviceId) {

    console.log("casesDialogCheckBoxChange : " + stage + ", Device Id :" + deviceId);
    console.log("Global Id: " + deviceSelected);
    console.log("Selector" + deviceSelected);

    // Escape class names that start with a number by using attribute selector
    const classSelector = `.${CSS.escape(stage)}.active-checkbox`;

    // Check if any checkbox with the specified class is checked
    if ($(classSelector + ":checked").length > 0) {

        $(`.activeSubmitBtn-${deviceId}`).prop('disabled', false);
        $(`.${CSS.escape(stage)}.blackbox-button-outer.device-${deviceId}`).removeClass('disabled').addClass(
            'enabled');
        $(`.${stage}.blackbox-button-inner.device-` + deviceId).removeClass('disabled').addClass('enabled');
        // Perform action when at least one checkbox is checked
    } else {
        console.log("A-Disable Btn Selector : " + `.${CSS.escape(key)}.blackbox-button-outer`);
        $(`.activeSubmitBtn-${deviceId}`).prop('disabled', true);
        $(`.${stage}.blackbox-button-outer`).removeClass('enabled').addClass('disabled');
        $(`.${stage}.blackbox-button-inner`).removeClass('enabled').addClass('disabled');
    }
}


//4.16.2025.9AM FML
// Bind delegated click event to detect clicks on disabled 3DPrinting checkboxes.
$(document).on('click', 'multipleCB.3dprinting', function (e) {
    caseIDFromOldDialog = 0;


    if ($(this).prop('disabled')) {
        e.preventDefault(); // Prevent any default action.
        showToast("Case already selected");
    }
});

function showToast(message) {
    var toast = $('<div class="toast-alert">' + message + '</div>');
    $('body').append(toast);
    // toast.animate({top: "30%"}, 500).delay(3000).animate({top: 0}, 500, function () {
    //     $(this).remove();
    // });

    toast.css({
        position: 'fixed',
        top: '-100px', // start off-screen
        left: '50%',
        transform: 'translateX(-50%)',
        opacity: 0
    }).animate({
        top: '20px', // slide into view
        opacity: 1
    }, 500).delay(3000).animate({
        top: '-100px', // slide back out
        opacity: 0
    }, 500, function () {
        $(this).remove();
    });
}

function multiCBChanged(groupKey, changedCheckbox) {
    // if (groupKey === "3DPrinting") {
    //     // Select all checkboxes with group "3DPrinting"
    //     var printingCheckboxes = document.querySelectorAll('.multipleCB.\\33 DPrinting');
    //
    //     if (changedCheckbox.checked) {
    //         // When one checkbox is checked, disable all others in the group.
    //         printingCheckboxes.forEach(function(checkbox) {
    //             if (checkbox !== changedCheckbox) {
    //                 checkbox.disabled = true;
    //             }
    //         });
    //     } else {
    //         // When unchecked, re-enable all checkboxes.
    //         printingCheckboxes.forEach(function(checkbox) {
    //             checkbox.disabled = false;
    //         });
    //     }
    //
    //     // Log checked values for processing (if needed)
    //     var checkedValues = Array.from(document.getElementsByName(changedCheckbox.name))
    //         .filter(function(cb) { return cb.checked; })
    //         .map(function(cb) { return cb.value; });
    //     console.log("Checked values for " + groupKey + ":", checkedValues);
    // }

    // This part of the function handles the show/hide behavior for .receiveSelectBtn based on overall selection.
    if ($(`.multipleCB.${groupKey}:checkbox:checked`).length > 0) {
        if (!$('.receiveSelectBtn').is(":visible")) {
            $(`.receiveSelectBtn.${groupKey}`).css({
                "opacity": "0",
                "display": "flex"
            }).show().animate({
                opacity: 1
            }, 300);
        }
    } else {
        $(`.receiveSelectBtn.${groupKey}`).css({
            "opacity": "1",
            "display": "flex"
        }).animate({
            opacity: 0
        }, 300, function () {
            $(`.receiveSelectBtn.${groupKey}`).css({
                "display": "none"
            });
        });
    }
}


function selectAll(ele, classname) {
    if ($(ele).prop('checked')) {
        $('.multipleCB.' + classname).prop('checked', true);
    } else {
        $('.multipleCB.' + classname).prop('checked', false);
    }
    if ($('.multipleCB:checkbox').length > 0) {
        multiCBChanged(classname);
    } else {
        console.log('select all didnt call multipleCheckboxChanged');
    }
}



function initSunriseTable($table) {
    if ($table.data('dt-init')) return;
    if ($table.find('thead').length === 0 || $table.find('tbody').length === 0) {
        console.log("Table missing thead or tbody:", $table.attr('id') || "unnamed table");
        return;
    }
    try {
        const dt = $table.DataTable({
            searching: false,
            lengthChange: false,
            autoWidth: false
        });
        $table.addClass("nowrap hover compact stripe");
        $table.data('dt-init', true);
        $table.data('dt-instance', dt);
    } catch (e) {
        console.log("DataTable initialization error:", e);
    }
}

function adjustSunriseTables(container) {
    $(container).find('.sunriseTable').each(function () {
        const dt = $(this).data('dt-instance');
        if (dt) {
            dt.columns.adjust();
        }
    });
}

$(document).ready(function () {
    $('.sunriseTable').each(function () { initSunriseTable($(this)); });

    // Initialize scrollable containers
    $('.YSH-job-list-container').each(function() {
        // Add smooth scrolling behavior
        this.addEventListener('wheel', function(e) {
            if (e.deltaY !== 0) {
                e.preventDefault();
                this.scrollTop += e.deltaY;
            }
        }, { passive: false });
    });

    // Global modal handling - close on outside click or ESC key
    $(document).on('click', '.Tect
-workflow-modal', function(e) {
        // Only close if clicking directly on the modal background (not its children)
        if (e.target === this) {
            const modalId = $(this).attr('id');
            const deviceId = modalId.replace(/sinteringCasesModal|casesListDialog|-waiting/g, '');
            closeModal({
                id: modalId,
                isWaiting: modalId.includes('-waiting'),
                deviceId: deviceId,
                exactId: modalId
            });
        }
    });

    // Handle ESC key press to close modals
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            // Find visible modals
            const visibleModal = $('.Tect
-workflow-modal[style*="display: flex"]');
            if (visibleModal.length) {
                const modalId = visibleModal.attr('id');
                const deviceId = modalId.replace(/sinteringCasesModal|casesListDialog|-waiting/g, '');
                closeModal({
                    id: modalId,
                    isWaiting: modalId.includes('-waiting'),
                    deviceId: deviceId,
                    exactId: modalId
                });
            }
        }
    });


    // Main Tabs
    try {
        $(".macaw-aurora-tabs").macawTabs({
            autoVerticalOrientation: true,
            tabPanelTransitionLogic: true,
            tabPanelTransitionTimeoutDuration: 10
        });

        // Nested Tabs
        $(".macaw-silk-tabs").macawTabs({
            autoVerticalOrientation: false,
        });
        $(document).on('click', '[role="tab"]', function () {
            const target = $(this).attr('aria-controls');
            if (target) {
                setTimeout(() => adjustSunriseTables('#' + target), 150);
            }
        });

        // Add debug button to reset tabs (only in development)
        //     $('body').append('<div id="resetTabs" style="position: fixed; bottom: 10px; right: 10px; background: #f55; color: white; padding: 5px; border-radius: 5px; cursor: pointer; z-index: 9999; font-size: 12px;">Reset Tabs</div>');
        //     $('#resetTabs').on('click', function () {
        //         console.log("Resetting all tab cookies");
        //         for (let i = 1; i < 11; i++) {
        //             Cookies.remove('inner' + i);
        //         }
        //         Cookies.remove('activeOuterTab');
        //         location.reload();
        //     });
    } catch (e) {
        console.error("Error initializing tabs:", e);
    }
});


$(document).ready(function () {

    toggleButtonState();

    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // activate single outer tab =>
    var activeOuter = Cookies.get("activeOuterTab");

    // Set default if no cookie exists or if value is invalid
    if (!activeOuter || $('#' + activeOuter).length === 0) {
        // Default to first available stage
        activeOuter = $('[role="tab"][aria-controls$="label"]').first().attr('id') || 'design';
        console.log("Using default tab:", activeOuter);
    }

    // Always use lowercase for 3dprinting
    if (activeOuter && activeOuter.toLowerCase().includes('3dprinting')) {
        activeOuter = '3dprinting';
    }

    console.log("Activating outer tab:", activeOuter);

    var btn = $('#' + activeOuter);
    if (btn.length) {
        btn.attr('aria-selected', true);
        btn.removeAttr('tabindex');

        var tabPanel = $('#' + activeOuter + "label");
        if (tabPanel.length) {
            tabPanel.addClass('active');
            tabPanel.removeAttr('hidden');
        } else {
            console.log("Tab panel not found:", activeOuter + "label");
        }

        // Special handling for 3D printing
        if (activeOuter === '3dprinting') {
            // console.log("Special handling for 3D printing tab");
            // Try to activate both active and waiting tabs if needed
            //  $('#active-3dprinting').removeAttr('hidden').addClass('active');

            // Default to showing one of the tabs
            // let innerActiveTab = Cookies.get('inner3');
            // console.log("Trying to activate inner tab:", innerActiveTab);
            //
            // let innerTabBtn = $("[id='" + innerActiveTab + "']");
            // let innerTabPanel = $("[aria-labelledby='" + innerActiveTab + "']");
            // if (innerTabBtn.length && innerTabPanel.length) {
            //     innerTabBtn.attr('aria-selected', true);
            //     innerTabBtn.removeAttr('tabindex');
            //     innerTabPanel.addClass('active');
            //     innerTabPanel.removeAttr('hidden');
            // }
        }
    } else {
        console.log("Tab button not found:", activeOuter);
    }


    // activate multiple inner tabs =>
    for (let i = 1; i < 11; i++) {
        var activeInnerTab = Cookies.get('inner' + i);


        if (activeInnerTab == undefined) {
            activeInnerTab = 'active-design';
        }

        // Normalize ID for 3D printing
        if (activeInnerTab && activeInnerTab.toLowerCase().includes('3dprinting')) {
            activeInnerTab = activeInnerTab.replace(/3[dD][pP]rinting/i, '3dprinting');
        }



        var innerTabBtn = $("[id='" + activeInnerTab + "']");
        var innerTab = $("[aria-labelledby='" + activeInnerTab + "']");

        if (innerTabBtn.length && innerTab.length) {
            innerTab.addClass('active');
            innerTab.removeAttr('hidden');
            innerTabBtn.attr('aria-selected', true);
            innerTabBtn.removeAttr('tabindex');
        }
    }
});

$("[id^='active']").click(function (e) {
    // Store just the ID value (not prefixing with 'inner')
    Cookies.set('inner' + $(this).attr('href'), $(this).attr('id'));
    console.log("set cookie for : " + 'inner' + $(this).attr('href') + ' => ' + $(this).attr('id'));
});



let escapedKey = "NotAvailable";
$(document).ready(function () {
    enableAllChoices();
    disableTextInput();

    // Make the dialog responsive on window resize
    $(window).resize(function () {
        adjustDialogLayout();
    });

    // Initial layout adjustment
    adjustDialogLayout();
});

function adjustDialogLayout() {
    const dialogWidth = $('.blackbox-dialog').width();
    const deviceContainer = $('.device-container');

    // Adjust container layout based on screen width
    if (dialogWidth < 500) {
        deviceContainer.addClass('small-screen');
    } else {
        deviceContainer.removeClass('small-screen');
    }
}

//
// function capFirstLetter(str) {
//     return str === "3Dprinting" ? "3DPrinting" : str.charAt(0).toUpperCase() + str.slice(1);
// }

function selectOption(element, key, deviceId) {
    // key is stage (p)
    // deviceId is printer/milling id
    //device selected is global variable
    deviceSelected = deviceId;
    key = key.toLowerCase();

    let escapedKey = CSS.escape(key);


    //------------------------------------------
    //-----  PRINTERS --------------------------
    //------------------------------------------
    if (fuzzyMatch(key, "3dprinting")) {
        let numberOfEnabledPrinters = document.querySelectorAll(
            `.silicon-valley-choice.silicon-valley-enabled.${escapedKey}`).length;
        console.log("numberOfEnabledPrinters : " + numberOfEnabledPrinters);
        if (numberOfEnabledPrinters > 0) {
            // Clicked printer is already selected, if 2 or more enabled, dont turn it off (disable)
            if (element.classList.contains('silicon-valley-enabled')) {
                numberOfEnabledPrinters > 1 ? disableAllButChosen(element, deviceId) : disableAllChoicesOf(key);
                numberOfEnabledPrinters > 1 ? enableTextInput() : disableTextInput();
                //3D printing only
                disableButton(key, deviceId, true);

                console.log("element already selected, toggling..");
            } else {
                // Clicked a disabled printer

                disableAllButChosen(element, deviceId);

                console.log("at least 1 printer selected");
            }
        } else {
            console.log("No printer selected key is " + key);
            disableAllButChosen(element, deviceId);
            enableTextInput();
            disableButton(key, deviceId, true);
        }
    }
        //------------------------------------------
        //-----  NOT PRINTERS --------------------------
    //------------------------------------------
    else {
        // Original behavior for other types
        const allChoices = document.querySelectorAll('.silicon-valley-choice.' + CSS.escape(key));
        // true if EVERY element has the 'silicon-valley-enabled' class
        const allEnabled = [...allChoices].every(choice => choice.classList.contains('silicon-valley-enabled'));

        console.log("All choices : " + allChoices.length + " all enabled : " + allEnabled + "key: " + CSS.escape(
            key));
        if (allEnabled && allChoices.length != 1) {
            console.log("all choices have class silicon-valley-enabled");
            disableAllButChosen(element, key);

        } else if (element.classList.contains('silicon-valley-enabled')) {
            console.log("element has class silicon-valley-enabled");
            // Special case for single choice - don't disable it on second click, just add animation
            if (allChoices.length === 1) {
                // Add a pulse animation class
                element.classList.add('pulse-animation');
                // Remove the animation class after it completes
                setTimeout(() => {
                    element.classList.remove('pulse-animation');
                }, 800);
            } else {
                disableAllChoicesOf(CSS.escape(key));
            }
        } else {
            console.log("element does not have class silicon-valley-enabled");
            disableAllChoicesOf(CSS.escape(key));
            element.classList.add('silicon-valley-enabled');
            enableButton(CSS.escape(key));
        }


        // true if at least one element has the 'silicon-valley-enabled' class
        const isAnyChoiceEnabled = [...allChoices].some(choice => choice.classList.contains(
            'silicon-valley-enabled'));
        if (isAnyChoiceEnabled) {
            console.log("Some Choices is enabled");
            enableButton(CSS.escape(key), deviceId);

        } else {
            console.log(" No Choice is enabled");
            disableButton(CSS.escape(key), deviceId, true);
        }
    }
    // if (element.)
}

function disableAllButChosen(element, deviceId) {

    const allChoices = document.querySelectorAll(`.bb-outer-img.waiting-popup`);
    allChoices.forEach(choice => choice.classList.remove('silicon-valley-enabled'));
    element.classList.add('silicon-valley-enabled');

    // const allChoices = document.querySelectorAll(`.silicon-valley-choice.${escapedKey}`);
    // const allEnabled = [...allChoices].every(choice => choice.classList.contains('silicon-valley-enabled'));
    // if (allEnabled) {
    //     allChoices.forEach(choice => {
    //         if (choice !== element) {
    //             choice.classList.remove('silicon-valley-enabled');
    //         }
    //     });
    // }
}

function enableAllChoices() {
    document.querySelectorAll('.silicon-valley-choice:not(.silicon-valley-enabled)').forEach(choice => {
        choice.classList.add('silicon-valley-enabled');
    });
}

function disableAllChoicesOf(key) {
    document.querySelectorAll(`.${CSS.escape(key)}.silicon-valley-choice`).forEach(choice => {
        choice.classList.remove('silicon-valley-enabled');
    });
}

function disableAllRoundedButtons() {
    document.querySelectorAll(`.blackbox-button-inner, .blackbox-button-outer`).forEach(choice => {
        choice.classList.remove('enabled', 'blue', 'orange', 'green');
    });
}

function checkInput() {
    let color = 'orange';
    const input = document.getElementById('silicon-valley-input').value;
    if (input.trim() !== '') {
        document.getElementById('blackbox-btn-outer').classList.add('enabled', color);
        document.getElementById('blackbox-btn-inner').classList.add('enabled', color);
    } else {
        document.getElementById('blackbox-btn-outer').classList.remove('enabled', color);
        document.getElementById('blackbox-btn-inner').classList.remove('enabled', color);
    }
}

/*
|--------------------------------------------------------------------------
|                          disableButton
|--------------------------------------------------------------------------
*/
function disableButton(key, deviceId = "", isWaiting = false) {


    // delivery dialog has no rounded button
    if (key.indexOf('delivery') !== -1) {

        const submitButton = document.getElementById('assignDeliveryBtn');
        submitButton.disabled = true;
        submitButton.classList.add('btn-loading');

        // Optionally, update the button text to indicate processing
        // You might store the initial text if you wish to revert later


        setTimeout(() => {
            submitButton.classList.remove('disabled');
        }, 3000);
    }

    ///////////////
    else {
        // active dialog
        if (deviceId !== "" && !isWaiting) {

            $(`.activeSubmitBtn-${CSS.escape(deviceId)}`).removeClass('silicon-valley-enabled enabled  ').addClass(
                'disabled'); //activeSubmitBtn
            $(`.${CSS.escape(key)}.blackbox-button-outer.device-${CSS.escape(deviceId)}`).removeClass(
                'silicon-valley-enabled enabled  ').addClass('disabled');
            $(`.${CSS.escape(key)}.blackbox-button-inner.device-${CSS.escape(deviceId)}`).removeClass(
                'silicon-valley-enabled enabled  ').addClass('disabled');
        }
        // waiting dialog
        else {
            console.log("W-Disable Btn Selector : " + `.${CSS.escape(key)}.blackbox-button-outer`);
            //.${CSS.escape(key)}

            $(`.waiting-popup.blackbox-button-inner`).removeClass('silicon-valley-enabled enabled  ').addClass(
                'disabled');
            $(`.waiting-popup.blackbox-button-outer`).removeClass('silicon-valley-enabled enabled ',).addClass(
                'disabled');


        }

    }

    //TODO disable btn by currentId


}

/*
|--------------------------------------------------------------------------
|                          UNCHECK CHECKBOXES
|--------------------------------------------------------------------------
*/

function uncheckCheckboxes(deviceId) {
    const checkboxes = document.querySelectorAll(`.${CSS.escape(deviceId)}.active-cases-checkbox`);
    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            checkbox.checked = false;
            checkbox.dispatchEvent(new Event('change', {
                bubbles: true
            }));
        }
    });
}


function disableAllButton(key) {
    document.querySelector(`.blackbox-button-outer.waiting-popup`).classList.remove('enabled');
    document.querySelector(`.blackbox-button-inner.waiting-popup`).classList.remove('enabled');
}


function enableButton(key, deviceId = "") {
    console.log("enable button key : " + key + " deviceId : " + deviceId);

    console.log("Selector: " + `.${CSS.escape(key)}.blackbox-button-outer.waiting-popup`);

    // document.querySelector(`.${CSS.escape(key)}.blackbox-button-outer.waiting-popup`)
    //     .classList.add('enabled');
    // document.querySelector(`.${CSS.escape(key)}.blackbox-button-inner.waiting-popup`)
    //     .classList.add('enabled');
    //
    try {
        $(".Tect
-button." + CSS.escape(key)).removeClass('disabled');
        document.querySelector(`.${CSS.escape(key)}.blackbox-button-outer`)
            .classList.add('enabled');
        document.querySelector(`.${CSS.escape(key)}.blackbox-button-inner`)
            .classList.add('enabled');
    } catch (e) {
        $(".Tect
-button").removeClass('disabled');
        document.querySelector(`.blackbox-button-outer.waiting-popup`)
            .classList.add('enabled');
        document.querySelector(`.blackbox-button-outer.waiting-popup`)
            .classList.add('enabled');
    }
}

function disableTextInput() {
    try {
        document.getElementById('silicon-valley-input').disabled = true;
    } catch (e) {
        console.log("Tried disabling text input");
    }
}

function enableTextInput() {
    try {
        document.getElementById('silicon-valley-input').disabled = false;
    } catch (e) {
        console.log("Tried enabling text input");
    }
}

function resetDialogStatus({
                               stageType1,
                               isWaiting = true,
                               deviceId = 0,
                               exactId = null
                           }) {

    if (exactId != null) {
        console.log("resetting dialog by ID: " + exactId);
        $("#" + exactId + " .bb-outer-img").removeClass('silicon-valley-enabled');
        $("#" + exactId + " #blackbox-button-outer").removeClass(
            'silicon-valley-enabled enabled  blue orange green');
        $("#" + exactId + " #blackbox-button-inner").removeClass(
            'silicon-valley-enabled enabled blue orange green',);

        uncheckCheckboxes(deviceId);
        console.log("reset done for " + exactId + " stageType1 : " + stageType1 + " deviceId : " + deviceId);
    } else {
        let classSelector = (isWaiting ? '.waiting-popup' : '') + '.' + CSS.escape(stageType1) +
            '.silicon-valley-choice:not(.silicon-valley-enabled)';
        console.log("resetting dialog : " + stageType1);

        $(classSelector).addClass('silicon-valley-enabled');


        if ($(classSelector).length === 0) {
            console.log("No matching element found for selector: " + classSelector);
            //TODO reset by current ID
            // return;
        }
    }

    // reset all
    $(".Tect
-machine-card.selected").removeClass("selected");


    disableButton(stageType1, "", isWaiting);
    clearTextInput();

    disableTextInput();
    uncheckCheckboxes(deviceId)
}

function clearTextInput() {
    $('.Tect
-form-control').val('');
}

function toggleButtonStatus(key, forceState = null) {
    const outerButton = document.querySelector(`.${CSS.escape(key)}.blackbox-button-outer`);
    const innerButton = document.querySelector(`.${CSS.escape(key)}.blackbox-button-inner`);

    if (!outerButton || !innerButton) {
        console.warn(`Buttons not found for key: ${key}`);
        return;
    }

    // If forceState is provided, use it directly
    if (forceState !== null) {
        if (forceState) {
            outerButton.classList.add('enabled');
            innerButton.classList.add('enabled');
        } else {
            outerButton.classList.remove('enabled');
            innerButton.classList.remove('enabled');
        }
        return;
    }

    // Otherwise toggle based on current state
    const isEnabled = outerButton.classList.contains('enabled');
    if (isEnabled) {
        outerButton.classList.remove('enabled');
        innerButton.classList.remove('enabled');
    } else {
        outerButton.classList.add('enabled');
        innerButton.classList.add('enabled');
    }
}


function YSH_toggleBuild(event, clickedRow) {
    console.log("YSH_toggleBuild", clickedRow);

    // Collapse all other rows
    document.querySelectorAll('.YSH-build-row').forEach(row => {
        if (row !== clickedRow) {
            row.classList.remove('active');
            const body = row.querySelector('.YSH-build-body');
            if (body) {
                body.style.display = 'none';
            }
        }
    });

    // Toggle the clicked one
    const body = clickedRow.querySelector('.YSH-build-body');
    if (!body) return; // safety check

    const isVisible = body.style.display === 'block';

    if (isVisible) {
        body.style.display = 'none';
        clickedRow.classList.remove('active');
    } else {
        body.style.display = 'block';
        clickedRow.classList.add('active');
    }
}


//TODO ANIMATION
function openModal(id, waiting = false, caseId = 0,isSintering = false) {
    console.log("openModal parameters: id=" + id + ", waiting=" + waiting + ", caseId=" + caseId);
    let modal;
    currentModalId = id;
    console.log(" ---------FOUND BY ID GIVEN ID IS : " + id + (waiting ? "-waiting" : "") + "---------------  ");
    caseIDFromOldDialog = caseId;
    if (isSintering) {

         modal = document.getElementById('sinteringCasesModal');
    }

    else{

     modal = document.getElementById(id + (waiting ? "-waiting" : ""));}

    document.getElementById("caseIdFromWaitingDialog").value = caseIDFromOldDialog;



    if (modal)
    {
        console.log(modal);
        modal.style.display = 'flex';
        modal.classList.add('modal-active');
        modal.classList.add('fade-in-animation');

        modal.addEventListener('animationend', function handler() {
            console.log("animation ended");
            modal.classList.remove('fade-in-animation');
            modal.removeEventListener('animationend', handler);
        });

        // setTimeout(function() {
        //      modal.classList.remove('fade-in-animation');
        //     modal.style.display = 'flex';
        //     modal.classList.remove('modal-active');
        // }, 500); // close the modal after 500ms


        console.log("modal found " + id);
        if (id == "DeliveryDialog")
            modal.classList.add('active');
        modal.style.display = 'flex'; // or 'block' depending on your CSS
        modal.classList.add('show');
        modal.scrollIntoView({
            behavior: "smooth",
            block: "center"
        }); // optional: for animation
    } else {
        console.error("Modal not found:", id);
    }
}

function closeModal({
                        id,
                        isWaiting = false,
                        deviceId = 0,
                        exactId = null
                    }) {
    if (isWaiting)
        id = id + "-waiting";
    currentModalId = id;
    console.log("closing modal " + id);

    try {
        let modal = document.getElementById(id) ?? document.getElementById(exactId);
        if (modal) {
            console.log("Modal found ");
            console.log(modal);
            modal.classList.add('fade-out-animation');

            modal.addEventListener('animationend', function handler() {
                console.log("animation ended");
                modal.style.display = 'none';
                modal.classList.remove('fade-out-animation');
                modal.classList.remove('modal-active');
                modal.removeEventListener('animationend', handler);
            });
            setTimeout(function () {
                modal.style.display = 'none';
                modal.classList.remove('fade-out-animation');
                modal.classList.remove('modal-active');
                if (id == "DeliveryDialog")
                    modal.classList.remove('active');
            }, 500); // close the modal after 500ms
            // Todo reset by id
            //not escaped
            resetDialogStatus({
                stageType1: modal.id,
                isWaiting: false,
                deviceId: deviceId,
                exactId: exactId
            });
        }
    } catch (e) {
        console.log("Exception closing modal " + id);
        console.log("Error closing modal: " + e);
        document.querySelectorAll('div[role="dialog"]').forEach(el => {
            el.classList.add('fade-out-animation');

            el.style.display = 'none';
            el.classList.remove('fade-out-animation');

        });

        // Todo reset by id

        console.log("modal not found closing all by role.. id : " + id + " exception : " + e);
    }


}

function processWorkflowAction222(deviceId, type, actionType, action) {




    console.log(`Processing action: ${action} for ${actionType}(s) on device ${deviceId}`);

    const form = document.getElementById(`process-form-${deviceId}`);
    const itemsInput = document.getElementById(`selected-items-${deviceId}`);
    const actionTypeInput = document.getElementById(`action-type-${deviceId}`);
    const stageTypeInput = document.getElementById(`stage-type-${deviceId}`);

    let selectedItems = [];

    if (actionType === 'build') {
        // Get all selected build checkboxes
        const selectedBuilds = form.querySelectorAll('input[name="buildId"]:checked');

        if (!selectedBuilds.length) {
            alert('Please select at least one build to process.');
            return;
        }

        // Add build IDs to selected items
        selectedBuilds.forEach(build => {
            selectedItems.push(build.value);
        });

        // Set stage type for the request
        if (stageTypeInput) {
            stageTypeInput.value = type;
        }

    } else { // actionType is 'jobs'
        // Get all checked job checkboxes
        const checkedCheckboxes = $(`input[type="checkbox"]:checked[class~="Tect
-checkbox"][class~="${type}"]`);
        console.log( "checkedCheckboxes magic selector of 3d builds : " + checkedCheckboxes);


        if (checkedCheckboxes.length === 0) {
            alert('Please select at least one job.');
            return;
        }

        checkedCheckboxes.forEach(checkbox => {
            selectedItems.push(checkbox.value);
        });
    }

    if (selectedItems.length > 0) {
        itemsInput.value = selectedItems.join(','); // Populate the hidden input with comma-separated IDs
        actionTypeInput.value = action; // 'start' or 'complete'
        form.submit(); // Submit the form
    } else if (actionType !== 'build') { // Only show this if not in the build logic that's currently blocked
        alert('No items selected.');
    }
}

// Ensure the action button state is updated correctly on page load
// document.addEventListener('DOMContentLoaded', function() {
//    document.querySelectorAll('[id$="casesListDialog"]').forEach(dialog => {
//         const deviceId = dialog.id.replace('casesListDialog', '');
//         const type = dialog.dataset.type; // Assuming you can add data-type attribute to the dialog div
//         const isBuilds = dialog.dataset.isBuilds === 'true'; // Assuming data-is-builds attribute
//
//          // Only run for non-build dialogs initially to set correct state based on checkboxes
//       //  if (!isBuilds) {
//          //    updateActionButtonState(deviceId, type);
//      //   } else {
//             // For builds, the button starts disabled and is enabled when a radio button is clicked
//              // The click handler on the build row handles the radio button selection and enabling
//      //   }
//     });
//
//     // Reset global variables - Ensure these are not causing unintended side effects
//     // window.selectedBuildId = null; // Consider if this is necessary or causing issues
//     // selectedBuildId = null; // Consider if this is necessary or causing issues
// });

$(document).ready(function () {
    // Loop through each div with the class `Tect
-workflow-modal waiting`
    $(".Tect
-workflow-modal.active").each(function () {
        const modal = $(this); // The current modal
        const button = modal.find(".Tect
-button"); // Find the button within this modal

        // Function to check and set button state
        function updateButtonState() {
            const isAnyChecked = modal.find("input[type='checkbox']:checked").length > 0;
            button.prop("disabled", !isAnyChecked); // Enable if at least one checkbox is checked

            console.log("isAnyChecked" + isAnyChecked);
        }
        // Attach a change event listener to checkboxes in this modal
        modal.on("input", "input[type='checkbox']", updateButtonState);

        // Trigger the function on page load to initialize the button state
        updateButtonState();
    });
});


// $(document).ready(function () {
//     console.log("Listening for visible dialogs with role='dialog'");
//
//     // Use MutationObserver to detect changes in dialog visibility
//     const observer = new MutationObserver(function () {
//         $(".Tect
-workflow-modal[role='dialog']").each(function () {
//             const modal = $(this);
//
//             // Check if the modal is visible (display is 'flex')
//             const isVisible =
//                 modal.css("display") === "flex" && modal.is(":visible");
//
//             if (isVisible) {
//                 dialogOnScreen = modal; // Save the modal element in the global variable
//                 console.log("Dialog is now visible and stored in dialogOnScreen:", dialogOnScreen);
//             }
//         });
//     });
//
//     // Observe the entire document for changes in child elements and their attributes
//     observer.observe(document.body, { attributes: true, childList: true, subtree: true });
// });


// Add a new function to update the action button state for non-builds
// function updateActionButtonState(deviceId, type) {
//     const form = document.getElementById(`process-form-${deviceId}`);
//     const actionButton = document.getElementById(`actionXX-button-${deviceId}`);
//    console.log("updateActionButtonState = " + actionButton);
//    form.querySelectorAll("checkbox:checked")
//
//     const checkedCheckboxes = document.querySelectorAll(`input.Tect
-checkbox.${CSS.escape(type)}[type="checkbox"]:checked`);
//     console.log( "checkbox change = " + checkedCheckboxes);
//     if (actionButton) {
//          // Enable the button if any checkbox is checked
//         actionButton.disabled = checkedCheckboxes.length === 0;
//
//         // Update button text and color based on whether active jobs exist
//
//         // A more reliable way to check for active jobs in the *current* dialog:
//         const currentDialog = document.getElementById(`${deviceId}casesListDialog`);
//          const activeJobRowsInDialog = currentDialog.querySelectorAll('.Tect
-job-row[style*="--main-blue"]'); // Find rows with the active color
//         const hasActiveJobs = activeJobRowsInDialog.length > 0; // This check might need to be specific to the current dialog
//
//
//         const hasActiveJobsInDialog = activeJobRowsInDialog.length > 0;
//
//         // Special handling for inactive jobs (orange rows) - they are disabled if active jobs exist
//         const inactiveCheckboxes = currentDialog.querySelectorAll('.Tect
-job-row[style*="--main-orange"] input[name="jobId[]"]');
//         if (hasActiveJobsInDialog) {
//             inactiveCheckboxes.forEach(checkbox => checkbox.disabled = true);
//             console.log("found inactiveCheckboxes = " + inactiveCheckboxes.length);
//         } else {
//              inactiveCheckboxes.forEach(checkbox => checkbox.disabled = false);
//             console.log("no inactiveCheckboxes = " + inactiveCheckboxes.length);
//         }
//
//
//     } else {
//         console.error(`Action button for device ${deviceId} not found.`);
//     }
// }


// Function to enable the action button specifically for build selection
function enableActionButton(deviceId, type) {
    console.log(`enableActionButton called for deviceId: ${deviceId}, type: ${type}`);
    const actionButton = document.getElementById(`actionX-button-${deviceId}`);
    if (actionButton) {
        console.log(`Action button found:`, actionButton);
        actionButton.disabled = false;
        // For builds, the button text/color is determined by whether the build has started
        // This logic is handled in the Blade template based on $data['build']->started_at
    } else {
        console.error(`Action button for device ${deviceId} not found in enableActionButton.`);
    }
}

// Toggle build details visibility


function showNoJobsMessage() {
    // You can implement a small toast or modal here
    console.log("No jobs available for this device.");
    // alert("No jobs available for this device."); // Example
}
