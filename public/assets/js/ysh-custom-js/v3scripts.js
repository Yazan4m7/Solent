// workflow-functions.js

/**
 * Global state variables
 */
// let selectedMachineId = null;
// let selectedItems = [];
// let selectedBuildId = null;
// let currentStage = null;
// let dialogOnScreen = null;
// Global variables
window.dialogOnScreen = null;
window.selectedMachineId = null;
window.initialMachineSelection = true;

$('.Tect-build-radio').on('click', e => e.stopPropagation());

// const routeSetMultipleCases = "{{route('set-multiple-cases')}}";
// const routeActivateMultipleCases = "{{route('activate-multiple-cases')}}";
// const routeFinishMultipleCases = "{{route('finish-multiple-cases')}}";
// const routeAssignToDelivery = "{{route('assign-to-delivery-person')}}";
// const routeSetCasesOnPrinter = "{{route('set-cases-on-printer')}}";
// const routeActivate3DBuilds = "{{route('activate-3d-builds')}}";
// const routeFinish3DBuilds = "{{route('finish-3d-builds')}}";

/**
 * Handle click on a device in the devices block
 *
 * @param {HTMLElement} element - The clicked device element
 * @param {string} deviceId - The device ID
 * @param {string} type - The workflow type/stage
 */
function handl222eClick(element, deviceId, type) {
    console.log("Device clicked:", deviceId, "type:", type);

    // Open the device cases list dialog
    openDeviceDialog(deviceId, type);
}

function selectMachine(element, type, machineId) {
    console.log("Machine clicked:", type, machineId);

    try {
        // Check if this machine is already selected
        const isAlreadySelected = element.classList.contains('selected');

        // Create appropriate selector for machine cards
        const selector = '.Tect-machine-card.' + (type === '3dprinting' ? '\\33 dprinting' : type);
        const allMachines = document.querySelectorAll(selector);

        if (isAlreadySelected) {
            // User clicked on already selected machine - deselect it
            element.classList.remove('selected');
            window.selectedMachineId = null;

            // Update hidden field
            const deviceInput = document.getElementById('deviceId-' + type);
            if (deviceInput) {
                deviceInput.value = '';
            }

            // For 3D printing, disable and clear build name input
            if (type === '3dprinting') {
                const buildNameInput = document.getElementById('Tect-build-name-' + type);
                if (buildNameInput) {
                    buildNameInput.disabled = true;
                    buildNameInput.value = '';
                }
            }

            // Disable action button
            const actionButton = document.getElementById('Tect-action-button-' + type);
            if (actionButton) {
                actionButton.disabled = true;
            }
        } else {
            // User selected a new machine

            // Clear previous selections
            allMachines.forEach(machine => {
                machine.classList.remove('selected');
            });

            // Select the clicked machine
            element.classList.add('selected');
            window.selectedMachineId = machineId;

            // Update hidden field
            const deviceInput = document.getElementById('deviceId-' + type);
            if (deviceInput) {
                deviceInput.value = machineId;
            }

            // For stages that require build name
            if (type === 'milling' || type === '3dprinting' || type === 'pressing') {
                const buildNameInput = document.getElementById('Tect-build-name-' + type);
                if (buildNameInput) {
                    buildNameInput.disabled = false;
                    buildNameInput.focus();

                    // Check if build name already has a value
                    validateAndSetBuildName(type);
                }
            } else {
                // For other types, enable the action button directly
                const actionButton = document.getElementById('Tect-action-button-' + type);
                if (actionButton) {
                    actionButton.disabled = false;
                }
            }
        }
    } catch (error) {
        console.error("Error in selectMachine:", error);
    }
}

/**
 * Validate build name and update button state
 */
function validateAndSetBuildName(type) {

    // Skip validation for types that don't require build name
    // if (type !== 'milling' && type !== '3dprinting' && type !== 'pressing') return;

    const buildNameInput = document.getElementById(`Tect-build-name-` + type);
    const actionButton = document.getElementById('Tect-action-button-' + type);

    console.log("Validating build name for " + type + ", input: " + buildNameInput + ", button: " + actionButton);

    if (buildNameInput && actionButton) {
        // Button enabled only if machine is selected AND build name has value
        const hasBuildName = buildNameInput.value.trim() != '';
        const machineSelected = (window.selectedMachineId) !== null;
        // $(`#build-name-{type}`)
        console.log("hasBuildName: " + hasBuildName + ", build name value: " + buildNameInput.value.trim() + ", machineSelected: " + machineSelected);

        actionButton.disabled = !(machineSelected && hasBuildName);

    }
}

function validateWorkflowForm(type) {
    const actionButton = document.getElementById(`Tect-action-button-${type}`);
    const isValid = selectedMachineId !== null;

    // For stages that require build name, check that it's provided
    if (type === 'milling' || type === '3dprinting' || type === 'pressing') {
        const buildNameInput = document.getElementById('Tect-build-name-' + type);
        const buildNameValue = buildNameInput ? buildNameInput.value.trim() : '';

        if (buildNameValue === '') {
            actionButton.disabled = true;
            return;
        }
    }

    // Enable/disable action button based on validation
    actionButton.disabled = !isValid;
}

/**
 * Initialize the dialog when it opens
 */
function initializeDialog(type) {
    console.log("Initializing dialog for:", type);

    // Reset selected machine
    window.selectedMachineId = null;

    // Reset all machine cards
    const selector = '.Tect-machine-card.' + (type === '3dprinting' ? '\\33 dprinting' : type);
    const allMachines = document.querySelectorAll(selector);

    allMachines.forEach(machine => {
        machine.classList.remove('selected');
        machine.classList.remove('disabled'); // Ensure no machines are disabled initially
    });

    // For stages that require build name, reset and disable build name input
    if (type === 'milling' || type === '3dprinting' || type === 'pressing') {
        const buildNameInput = document.getElementById('Tect-build-name-' + type);
        if (buildNameInput) {
            buildNameInput.disabled = true;
            buildNameInput.value = '';
        }
    }

    // Disable action button
    const actionButton = document.getElementById('Tect-action-button-' + type);
    if (actionButton) {
        actionButton.disabled = true;
    }
}

/**
 * Submit the workflow
 */
// Update the submitWorkflow function
function submitWorkflow(type) {
    console.log("Submitting workflow for type:", type);

    // Check if machine is selected
    if (!window.selectedMachineId) {
        showToast("Please select a machine", "warning");
        return;
    }

    // Show loading indicator
    showLoadingIndicator();

    const routeSetMultipleCases = "/set-multiple-cases";
    const routeActivateMultipleCases = "/finish-multiple-cases";
    const routeFinishMultipleCases = "/finish-multiple-cases";
    const routeAssignToDelivery = "/assign-to-delivery-person";
    const routeSetCasesOnPrinter = "/set-cases-on-printer";
    const routeActivate3DBuilds = "/activate-3d-builds";
    const routeFinish3DBuilds = "/finish-3d-builds";

    // Get build name for stages that require it
    let buildName = "";
    try {
         if (type === 'milling' || type === '3dprinting' || type === 'pressing') {
        console.log('Getting build name for: ' + type);
        const buildNameInput = document.getElementById('Tect-build-name-' + type);
        buildName = buildNameInput ? buildNameInput.value.trim() : "";

        // Validate build name for these stages
        if (!buildName) {
            hideLoadingIndicator();
            showToast("Please enter a build name", "warning");
            return;
        }
            }
    } catch (error) {
        hideLoadingIndicator();
        console.error("Error getting build name:", error);
        showToast("Error getting build name for " + type, "error");
        return;
    }


    // Update build name field for all stages that need it
    if (type === 'milling' || type === '3dprinting' || type === 'pressing') {
        const buildNameField = document.getElementById('Tect-build-name-' + type);
        if (buildNameField) {
            buildNameField.value = buildName;
            console.log('Set build name for ' + type + ' to: ' + buildName);
        } else {
            hideLoadingIndicator();
            console.error("Build name field not found:", 'build-name-' + type);
            showToast("Error: Build name field not found", "error");
            return;
        }
    }

    // SET HIDDEN BUILD NAME TO BE SENT TO BACKEND
    $(`#build-name-${type}`).val(buildName);


    // Set the correct route based on type
    const hiddenForm = document.getElementById('hidden-form-' + type);
    if (!hiddenForm) {
        hideLoadingIndicator();
        console.error("Form not found:", 'hidden-form-' + type);
        showToast("Error: Form not found", "error");
        return;
    }

    if (type === '3dprinting') {
        hiddenForm.action = routeSetCasesOnPrinter;
    } else if (type === 'delivery') {
        hiddenForm.action = routeAssignToDelivery;
    } else {
        hiddenForm.action = routeSetMultipleCases;
    }


    // Update hidden machine ID field
    const deviceIdField = document.getElementById('device-id-' + type);
    if (deviceIdField) {
        deviceIdField.value = window.selectedMachineId;
    } else {
        hideLoadingIndicator();
        console.error("Device ID field not found:", 'device-id-' + type);
        showToast("Error: Device ID field not found", "error");
        return;
    }


    // Get checked cases
    const checkedCases = getCheckedValues(type);
    if (!checkedCases || checkedCases.length === 0) {
        hideLoadingIndicator();
        showToast("No cases selected. Please select at least one case.", "warning");
        return;
    }
    const caseIdsField = document.getElementById('case-ids-' + type);
    if (caseIdsField) {
        caseIdsField.value = checkedCases.join(',');
    } else {
        hideLoadingIndicator();
        console.error("Case IDs field not found:", 'case-ids-' + type);
        showToast("Error: Case IDs field not found", "error");
        return;
    }


    console.log("Submitting form with:", {
        type: type,
        deviceId: window.selectedMachineId,
        buildName: buildName,
        caseIds: checkedCases.join(',')
    });

    // Submit the form
    hiddenForm.submit();
}

function submitDeviceDialog(deviceId, type, itemType, actionType) {


    showLoadingIndicator();

    let form = $(`#process-form-${deviceId}`);
    let hiddenFieldToBeSubmitted = $(`.buildsIdsHiddenInput${deviceId}`);
    if (hiddenFieldToBeSubmitted.val().length === 0) {
        let itemsInputValue = $(`.active-values-holder-${deviceId}`)
            .map((i, element) => $(element).val())
            .get();
        console.log("itemsInputValue from document: active holders : " + itemsInputValue);
        if (itemsInputValue.length === 0) {
            itemsInputValue = dialogOnScreen
                .find(".value-holder[type='hidden']")
                .map((i, element) => $(element).val())
                .get();
            console.log("itemsInputValue from dialogOnScreen: active holders : " + itemsInputValue);
        }
        itemsInputValue = $(`.checkboxes-group-${deviceId}:checked`)
            .map((i, element) => $(element).val())
            .get();


        if(itemsInputValue.length === 0){
            showToast(`Please select a build/case to ${actionType}`, 'warning');
            hideLoadingIndicator();
            return;
        }


        hiddenFieldToBeSubmitted.val(itemsInputValue);
    }




    // Populate the hidden input with comma-separated IDs


    $("#action-type-" + deviceId).val(actionType); // Set the action type // 'start' or 'complete'
    form.attr("action", getFormActionType(actionType, type, itemType));
    console.log("FORM DATA in Front end: " + form.serialize());
    form.submit();
    // document.getElementById(`.#process-form-${deviceId}`).submit();
// Submit the form
    hideLoadingIndicator();
}
$(document).on('change', '.single-choice', function () {
    let $clicked = $(this);
    let groupId = $clicked.data('group-id'); // device id or stage name
    let actionButton = $("#actionXX-button-" + groupId);
    let dialogCheckBoxes = $(`.checkboxes-group-${groupId}`);
    console.log('[single-choice listener]: Checkbox clicked:', $clicked.val() +  ' Group ID:', groupId);

    // Uncheck all checkboxes in the same group, except the one clicked
    dialogCheckBoxes.not($clicked).prop('checked', false);

    if (dialogCheckBoxes.filter(':checked').length == 0)
        actionButton.prop('disabled', true);
    else
        actionButton.prop('disabled', false);
    const $input = $(".buildsIdsHiddenInput" + groupId);

    const checkedValues = dialogCheckBoxes.filter(':checked').map(function(){ return $(this).val(); }).get();
    console.log('[single-choice listener]: Checked values:', checkedValues);
    $input.val(checkedValues.join(','));
    // Sets hidden value to submits
    // saveCheckedCheckboxes( $clicked[0],groupId);
});

$(document).ready(function () {
    console.log("Listening for visible dialogs with role='dialog'");

    const observer = new MutationObserver(function () {
        $(".Tect-workflow-modal[role='dialog']").each(function () {
            const modal = $(this);

            // Check visibility of modal
            const isVisible = modal.css("display") === "flex" && modal.is(":visible");

            if (isVisible) {
                if (dialogOnScreen !== modal) {
                    dialogOnScreen = modal; // Save modal in global variable
                    window.selectedBuildId = modal.data("buildid") || null; // Store 'buildId' if present

                    //console.log("Dialog is now visible and stored in dialogOnScreen:", dialogOnScreen);
                    //  console.log("Selected Build ID (if available):", window.selectedBuildId);

                    // Get values of all hidden inputs with class 'hv-holders' inside the modal
                    const hiddenValues = modal
                        .find(".value-holder[type='hidden']")
                        .map((i, element) => $(element).val())
                        .get();

                    // console.log("Hidden input values in visible modal:", hiddenValues);

                    // Optionally, do something with `hiddenValues` if needed
                }
            } else if (!isVisible && dialogOnScreen === modal) {
                // Reset variables when the modal is hidden
                console.log("Dialog is now hidden. Resetting dialogOnScreen and selectedBuildId.");
                dialogOnScreen = null;
                window.selectedBuildId = null;
            }
        });
    });

    // Observe the DOM for changes
    observer.observe(document.body, {attributes: true, childList: true, subtree: true});
});

/**
 * Get checked cases from the parent page
 *
 * @param {string} type - The workflow type/stage
 * @returns {Array} - Array of case IDs
 */
function getCheckedCases(type) {
    // Try to get from parent window if available
    if (window.parent && window.parent.getCheckedValues) {
        return window.parent.getCheckedValues(type);
    }

    // Fallback to local method
    const checkboxes = document.querySelectorAll(`.multipleCB.${CSS.escape(key)}:checked`);
    return Array.from(checkboxes).map(cb => cb.value);
}

/**
 * Open device dialog in the active tab
 *
 * @param {string} deviceId - The device ID
 * @param {string} type - The workflow type/stage
 */
function openDeviceDialog(deviceId, type) {
    currentStage = type;
    console.log(`Opening device dialog for device ID: ${deviceId}, type: ${type}`);

    // Troubleshooting code - add more logging for 3D printing
    if (type === '3dprinting') {
        console.log('Opening 3D printing device dialog for ID: ' + deviceId);
    }

    // Try all possible dialog ID formats (most specific to least specific)
    const possibleDialogIds = [
        `${deviceId}casesListDialog`,  // This is the standard format we're using
        `${type}-${deviceId}-dialog`,
        `${deviceId}-${type}-dialog`,
        `${deviceId}-cases-dialog`,
        `device-${deviceId}-dialog`,
        `${deviceId}dialog`,
        `${deviceId}sinteringCasesModal`
    ];

    let dialog = null;
    for (const id of possibleDialogIds) {
        const foundDialog = document.getElementById(id);
        if (foundDialog) {
            dialog = foundDialog;
            console.log(`Found dialog with ID: ${id}`);
            break;
        } else {
            console.log(`Dialog ID not found: ${id}`);
        }
    }

    if (!dialog) {
        console.error(`No dialog found for device ID: ${deviceId} and type: ${type}`);
        showToast("Error: Could not find dialog for this device", "error");
        return;
    }

    // Reset selection
    selectedItems = [];
    selectedBuildId = null;

    // Add special class for delivery dialog to apply enhanced styling
    if (type === 'delivery') {
        dialog.classList.add('delivery-dialog');
    }

    // Show dialog with animation
    dialog.classList.add('active', 'show');
    dialog.style.display = 'flex';
    const dialogContent = dialog.querySelector('.Tect-workflow-dialog') || dialog.querySelector('.modal-content');
    if (dialogContent) {
        dialogContent.classList.add('fade-in');
        // Remove animation class after animation completes
        setTimeout(() => {
            dialogContent.classList.remove('fade-in');
        }, 300);
    }

    // Set up case list with improved visuals for delivery
    if (type === 'delivery') {
        enhanceDeliveryDialog(dialog, deviceId);
    }

    // Reset action button state
//updateActionButtonState(deviceId, type);
}

function saveCheckedCheckboxes(el, deviceId) {
    const val = el.value, checked = el.checked;
    const $input = $(".buildsIdsHiddenInput" + deviceId);

    let arr = $input.val()?.split(",").filter(Boolean) || [];
    console.log("Current value of IDs: " +$input.val());
    console.log("checked is " +checked);
    if (checked && !arr.includes(val)) arr.push(val);
    else if (!checked) arr = arr.filter(v => v != val);

    $input.val(arr.join(','));
    console.log("New List of IDs: " +$input.val());
}


function handleClick(element, deviceId, type) {
    deviceSlected = deviceId;
    // Trigger the animation
/* <<<<<<<<<<<<<<  ✨ Windsurf Command ⭐ >>>>>>>>>>>>>>>> */
    element.classList.toggle('selected');
/* <<<<<<<<<<  7cc2b476-fc3c-47d9-9f23-3d8b94c9de11  >>>>>>>>>>> */

    const img = document.querySelector('.clickable-image');
    if (img) {
        img.classList.add('clicked');

        // Remove the animation class after the animation ends
        img.addEventListener('animationend', () => {
            img.classList.remove('clicked');
        }, {
            once: true
        });
    }

    console.log("Handling click");

    // Execute the old onclick functionality (openModal)
    if (deviceId) {
        console.log("dialog in " + deviceId);
        var modalId = deviceId + "casesListDialog";
        console.log("Opening device modal: ", modalId);
        openDeviceDialog(deviceId, type);
    }
}

function getFormActionType(dialogBtnTxt, stageName, buildOrCase) {
    // Set the action type (build or jobs)

    console.log("dialogBtnTxt: " + dialogBtnTxt + "  stageName: " + stageName + "  buildOrCase: " + buildOrCase);
    ;
    dialogBtnTxt = dialogBtnTxt.toLowerCase();
    let action;

    // Set the appropriate form action based on type and action type
    if (stageName === '3dprinting' && buildOrCase === 'build') {
        if (dialogBtnTxt === 'start') {
            action = "/activate-3d-builds";
            console.log("Setting form action to activate-3d-builds");
        } else {
            action = "/finish-multiple-cases";
            console.log("Setting form action to /finish-multiple-cases");
        }
    } else if (stageName === 'milling' && dialogBtnTxt === 'complete') {
        // For milling completion, we'll directly call finish-multiple-cases
        action = "/finish-multiple-cases";
        console.log("Setting form action to finish-multiple-cases with explicit milling type");
    } else {
        if (dialogBtnTxt === 'start') {
            action = "/activate-multiple-cases";
            console.log("Setting form action to /activate-multiple-cases activate-multiple-cases");
        } else {
            action = "/finish-multiple-cases";
            console.log("Setting form action to finish-multiple-cases");
        }
    }


    console.log("#############  FORM SUBMITTING TO " + action);
    return action;
}

$(".multipleCB").on("change", function () {

    console.log("weird function triggered" + `$(".multipleCB").on("change"`);
    const val = this.value; // ex: "123_A"
    const [deviceId, actualVal] = val.split("_");
    const $this = $(this);
    const $input = $(".buildsIdsHiddenInput" + deviceId);

    const isSingleGroup = $this.hasClass(deviceId); // this is your single-value group

    if (isSingleGroup) {
        // Uncheck all others in the same single-select group
        $(".multipleCB." + deviceId).prop("checked", false);
        this.checked = true; // re-check current one
        $input.val(val); // save the selected value
    } else {
        // Handle multiple selections
        let arr = $input.val()?.split(",").filter(Boolean) || [];

        if (this.checked) {
            if (!arr.includes(val)) arr.push(val);
        } else {
            arr = arr.filter(v => v !== val);
        }

        $input.val(arr.join(","));
    }
});




/**
 * Close device dialog
 *
 * @param {string} deviceId - The device ID
 */
function closeDeviceDialog(deviceId) {
    // Try all possible dialog IDs
    const possibleDialogIds = [
        `${deviceId}-cases-dialog`,
        `device-${deviceId}-dialog`,
        `${deviceId}casesListDialog`,
        `${deviceId}sinteringCasesModal`
    ];

    let dialog = null;
    for (const id of possibleDialogIds) {
        const foundDialog = document.getElementById(id);
        if (foundDialog) {
            dialog = foundDialog;
            break;
        }
    }

    if (!dialog) {
        console.error(`No dialog found for device ID: ${deviceId}`);
        return;
    }

    // Add fade-out animation
    const dialogContent = dialog.querySelector('.Tect-workflow-dialog') || dialog.querySelector('.modal-content');
    if (dialogContent) {
        dialogContent.classList.add('fade-out');
    }

    // Hide dialog after animation completes
    setTimeout(() => {
        dialog.classList.remove('active');
        dialog.style.display = 'none';
        if (dialogContent) {
            dialogContent.classList.remove('fade-out');
        }
    }, 300);
}

/**
 * Toggle build details visibility
 *
 * @param {HTMLElement} header - The build header element
 */
function toggleBuildDetails(header) {
    const buildRow = header.closest('.Tect-build-row');
    buildRow.classList.toggle('expanded');

    // Rotate chevron
    // const chevron = header.querySelector('.Tect-build-toggle i');
    // chevron.style.transform = buildRow.classList.contains('expanded') ? 'rotate(180deg)' : 'rotate(0)';
    //   const buildRow = headerElement.closest('.Tect-build-row');
    const details = buildRow.querySelector('.-build-details');
    const toggleIcon = header.querySelector('.Tect-build-toggle i');

    if (details.style.display === 'block') {
        details.style.display = 'none';
        toggleIcon.classList.remove('fa-chevron-up');
        toggleIcon.classList.add('fa-chevron-down');
    } else {
        details.style.display = 'block';
        toggleIcon.classList.remove('fa-chevron-down');
        toggleIcon.classList.add('fa-chevron-up');
    }
}

/**
 * Enable action button when a build is selected
 *
 * @param {string} deviceId - The device ID
 * @param {string} type - The workflow type/stage
 */
function enableActionButton(deviceId, type) {
    const actionButton = document.getElementById(`actionX-button-${deviceId}`);
    if (actionButton) {
        actionButton.disabled = false;
    }

    // For 3D printing, store selected build ID
    if (type === '3dprinting') {
        // Look for any radio with name buildId
        const selectedRadio = document.querySelector('input[name="buildId"]:checked');

        if (selectedRadio) {
            selectedBuildId = selectedRadio.value;
            window.selectedBuildId = selectedRadio.value; // Also store in window for global access
            console.log('Selected build ID:', selectedBuildId, 'for device:', deviceId);

            // Make sure the action button is enabled
            if (actionButton) {
                actionButton.disabled = false;
                console.log('Action button enabled for device', deviceId);
            }
        } else {
            console.log('No build selected for 3D printing');
            if (actionButton) {
                actionButton.disabled = true;
            }
        }
    }
}


/**
 * Update action button state based on selection
 *
 * @param {string} deviceId - The device ID
 * @param {string} type - The workflow type/stage
 */
function updateActiXXXXXXXXonButtonState(deviceId, type) {
    const actionButton = document.getElementById(`actionX-button-${deviceId}`);
    if (!actionButton) return;

    // Check if any active (blue) jobs exist
    const activeJobs = document.querySelectorAll(`.Tect-checkbox.active-blue-row`);
    const hasActiveJobs = activeJobs.length > 0;

    // If we have active jobs
    if (hasActiveJobs) {
        // Ensure all active jobs are checked
        activeJobs.forEach(checkbox => {
            checkbox.checked = true;
        });

        // Disable all inactive jobs
        document.querySelectorAll(`.Tect-checkbox.inactive-orange-row`).forEach(checkbox => {
            checkbox.disabled = true;
        });

        // For checkbox selection (jobs) - only get active jobs
        selectedItems = Array.from(activeJobs).map(cb => cb.value);

        // Enable the action button if there are active jobs
        actionButton.disabled = false;
        actionButton.textContent = 'COMPLETE';
        actionButton.style.backgroundColor = 'var(--main-green)';
    } else {
        // For checkbox selection (jobs) - get all checked jobs
        const checkedBoxes = document.querySelectorAll(`.Tect-checkbox:checked`);
        selectedItems = Array.from(checkedBoxes).map(cb => cb.value);

        // Disable button if no jobs are selected
        actionButton.disabled = selectedItems.length === 0;
        actionButton.textContent = 'START';
        actionButton.style.backgroundColor = 'var(--main-blue)';
    }
}

/**
 * Process workflow action (start or complete)
 *
 * @param {string} deviceId - The device ID
 * @param {string} type - The workflow type/stage
 * @param {string} itemType - Item type ('build' or 'jobs')
 * @param {string} actionType - The action to perform ('start' or 'complete')
 */
function processWorkflowAction(deviceId, type, itemType, actionType) {
    let formData = [];
    let checkboxes = [];

    console.log("Processing workflow action for device: " + deviceId + ", type: " + type + ", itemType: " + itemType + ", actionType: " + actionType);

    // Get all checked checkboxes
    document.querySelectorAll('input[name="jobId[]"]:checked').forEach((checkbox) => {
        const value = checkbox.value;
        if (!checkboxes.includes(value)) {
            checkboxes.push(value);
        }
    });

    // Get selected build ID from global or from DOM
    if (!selectedBuildId) {
        // Try to get it from the window variable
        selectedBuildId = window.selectedBuildId;

        // If not found, try to get from DOM
        if (!selectedBuildId) {
            const selectedRadio = document.querySelector('input[name="buildId"]:checked');
            if (selectedRadio) {
                selectedBuildId = selectedRadio.value;
            }
        }
    }
    selectedBuildId = $()

    console.log("Selected build ID:", selectedBuildId);

    if (itemType === 'build') {
        // For 3D printing builds
        if (!selectedBuildId) {
            showToast('Please select a build', 'warning');
            return;
        }

        formData.push(selectedBuildId);
        console.log("Will process build with ID:", selectedBuildId);
    } else {
        // For regular jobs
        if (checkboxes.length === 0) {
            // Try to get selected jobs from the global selectedItems array
            checkboxes = selectedItems;

            // If still empty, try the backup method
            if (checkboxes.length === 0) {
                checkboxes = getSelectedJobIds();

                // If we still have no jobs selected, show a warning
                if (checkboxes.length === 0) {
                    showToast('Please select at least one job', 'warning');
                    return;
                }
            }
        }

        formData = checkboxes;
    }

    // Populate hidden form with selected items
    document.getElementById(`selected-items-${deviceId}`).value = formData.join(',');

    // Set form action based on type and action
    const form = document.getElementById(`process-form-${deviceId}`);

    // Add the stage type as a hidden field to ensure proper build ID is used
    let stageTypeField = document.getElementById(`stage-type-${deviceId}`);
    if (!stageTypeField) {
        stageTypeField = document.createElement('input');
        stageTypeField.type = 'hidden';
        stageTypeField.name = 'stage_type';
        stageTypeField.id = `stage-type-${deviceId}`;
        form.appendChild(stageTypeField);
    }

    stageTypeField.value = type;

    // Add action type field if it doesn't exist
    let actionTypeField = document.getElementById(`action-type-${deviceId}`);
    if (!actionTypeField) {
        actionTypeField = document.createElement('input');
        actionTypeField.type = 'hidden';
        actionTypeField.name = 'action';
        actionTypeField.id = `action-type-${deviceId}`;
        form.appendChild(actionTypeField);
    }

    // Set the action type (build or jobs)
    actionTypeField.value = itemType;

    console.log("Setting action type to:", itemType);

    // Set the appropriate form action based on type and action type
    if (type === '3dprinting' && itemType === 'build') {
        if (actionType === 'start') {
            form.action = "/activate-3d-builds";
            console.log("Setting form action to activate-3d-builds");
        } else {
            form.action = "/finish-3d-builds";
            console.log("Setting form action to finish-3d-builds");
        }
    } else if (type === 'milling' && actionType === 'complete') {
        // For milling completion, we'll directly call finish-multiple-cases
        form.action = "/finish-multiple-cases";
        // Make sure the correct stage_type is set
        stageTypeField.value = 'milling';
        console.log("Setting form action to finish-multiple-cases with explicit milling type");
    } else {
        if (actionType === 'start') {
            form.action = "/activate-multiple-cases";
            console.log("Setting form action to activate-multiple-cases");
        } else {
            form.action = "/finish-multiple-cases";
            console.log("Setting form action to finish-multiple-cases");
        }
    }

    console.log("#############  FORMXXX SUBMITTING TO " + form.action);

    // Show loading state
    showLoadingIndicator();

    // Submit the form
    form.submit();
}

/**
 * Show toast notification
 *
 * @param {string} message - Message to display
 * @param {string} type - Toast type (success, error, warning, info)
 */
function showToast(message, type = 'info') {
    // Remove existing toasts
    const existingToasts = document.querySelectorAll('.Tect-toast');
    existingToasts.forEach(toast => {
        document.body.removeChild(toast);
    });

    // Create new toast
    const toast = document.createElement('div');
    toast.className = `Tect-toast ${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);

    // Show toast with animation
    setTimeout(() => {
        toast.classList.add('visible');
    }, 10);

    // Auto-hide after 3 seconds
    setTimeout(() => {
        toast.classList.remove('visible');

        // Remove from DOM after animation
        setTimeout(() => {
            if (toast.parentNode) {
                document.body.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

/**
 * Show loading indicator
 */
function showLoadingIndicator() {
    let loadingIndicator = document.getElementById('Tect-loading-indicator');

    if (!loadingIndicator) {
        loadingIndicator = document.createElement('div');
        loadingIndicator.id = 'Tect-loading-indicator';
        loadingIndicator.className = 'Tect-loading-overlay';
        loadingIndicator.innerHTML = `

        `;
        document.body.appendChild(loadingIndicator);
    }

    loadingIndicator.classList.add('active');
    console.log('Loading indicator shown');

    // Add safety timeout to hide the indicator after 15 seconds
    // in case the form submission fails silently
    setTimeout(() => {
        if (document.getElementById('Tect-loading-indicator')?.classList.contains('active')) {
            console.log('Safety timeout: hiding loading indicator after 15s');
            hideLoadingIndicator();
            showToast('The operation is taking longer than expected. Please check if it completed.', 'warning');
        }
    }, 25000);
}

/**
 * Hide loading indicator
 */
function hideLoadingIndicator() {
    const loadingIndicator = document.getElementById('Tect-loading-indicator');
    if (loadingIndicator) {
        loadingIndicator.classList.remove('active');
    }
}

/**
 * View case details by opening case page
 *
 * @param {number} caseId - The case ID
 */
function viewCase(caseId) {
    window.location.href = `/view-case/${caseId}/stage/${currentStage}`;
}

/**
 * Show a message when trying to interact with a machine that has no jobs
 */
function showNoJobsMessage() {
    showToast('This machine has no jobs assigned to it.', 'info');
}

/**
 * Open a modal dialog
 *
 * @param {string} id - The modal ID
 * @param {boolean} isWaiting - Whether this is a waiting modal
 * @param {number} caseId - Optional case ID to pre-populate
 //  */
// function openModal(id, isWaiting = false, caseId = 0) {
//     const modalId = id + (isWaiting ? "-waiting" : "");
//     const modal = document.getElementById(modalId);
//
//     if (!modal) {
//         console.error(`Modal not found: ${modalId}`);
//         return;
//     }
//
//     // Store case ID if provided
//     if (caseId > 0) {
//         window.caseIDFromOldDialog = caseId;
//     }
//
//     // Reset form state
//     selectedMachineId = null;
//     const machineCards = modal.querySelectorAll('.Tect-machine-card');
//     machineCards.forEach(card => card.classList.remove('selected'));
//
//     // Disable action button initially
//     const actionButtons = modal.querySelectorAll('.Tect-button');
//     actionButtons.forEach(btn => btn.disabled = true);
//
//     // Clear build name if present
//     const buildNameInput = modal.querySelector('.BNI');
//     if (buildNameInput) {
//         buildNameInput.value = '';
//     }
//
//     // Show modal with animation
//     modal.classList.add('active');
//     const dialogContent = modal.querySelector('.Tect-workflow-dialog') || modal.querySelector('.modal-content');
//     if (dialogContent) {
//         dialogContent.classList.add('fade-in');
//         // Remove animation class after animation completes
//         setTimeout(() => {
//             dialogContent.classList.remove('fade-in');
//         }, 300);
//     }
// }

// /**
//  * Close a modal dialog
//  *
//  * @param {Object} options - Options object
//  * @param {string} options.id - Modal ID
//  * @param {boolean} options.isWaiting - Whether this is a waiting modal
//  * @param {number} options.deviceId - Device ID (unused in this implementation)
//  * @param {string} options.exactId - Optional exact modal ID
//  */
// function closeModal(options) {
//     const {id, isWaiting = false, exactId = null} = options;
//
//     // Determine modal ID
//     const modalId = exactId || (id + (isWaiting ? "-waiting" : ""));
//     const modal = document.getElementById(modalId);
//
//     if (!modal) {
//         console.error(`Modal not found: ${modalId}`);
//         return;
//     }
//
//     // Add fade-out animation
//     const dialogContent = modal.querySelector('.Tect-workflow-dialog') || modal.querySelector('.modal-content');
//     if (dialogContent) {
//         dialogContent.classList.add('fade-out');
//     }
//
//     // Hide dialog after animation completes
//     setTimeout(() => {
//         modal.classList.remove('active');
//         model.style.display = 'none';
//         if (dialogContent) {
//             dialogContent.classList.remove('fade-out');
//         }
//     }, 300);
//
//     // Reset case ID from old dialog
//     window.caseIDFromOldDialog = 0;
// }

// /**
//  * Handle multiple case selection in waiting tab
//  *
//  * @param {string} type - The workflow type/stage
//  * @param {HTMLElement} checkbox - The changed checkbox
//  * @param {string} caseId - The case ID
//  */
// function multiCBChanged(type, checkbox, caseId) {
//     // For 3D Printing, enforce single selection
//     if (type === '3dprinting' && checkbox.checked) {
//         const checkboxes = document.querySelectorAll(`.multipleCB.${type}`);
//         checkboxes.forEach(cb => {
//             if (cb !== checkbox) {
//                 cb.checked = false;
//             }
//         });
//     }
//
//     // Show/hide SET button based on selection
//     const setButton = document.querySelector(`.receiveSelectBtn.${type}`);
//
//     if (setButton) {
//         const hasSelection = document.querySelectorAll(`.multipleCB.${type}:checked`).length > 0;
//
//         if (hasSelection && setButton.style.display !== 'flex') {
//             setButton.style.opacity = '0';
//             // setButton.style.display = 'flex';
//
//             // Fade in the button
//             setTimeout(() => {
//                 setButton.style.opacity = '1';
//             }, 10);
//         } else if (!hasSelection) {
//             // Fade out the button
//             setButton.style.opacity = '1';
//
//             setTimeout(() => {
//                 setButton.style.opacity = '0';
//
//                 // Hide button after fade out
//                 setTimeout(() => {
//                     setButton.style.display = 'none';
//                 }, 300);
//             }, 10);
//         }
//     }
// }
//
// /**
//  * Select or deselect all checkboxes
//  *
//  * @param {HTMLElement} element - The select all checkbox
//  * @param {string} className - The class name for checkboxes
//  */
// function selectAll(element, className) {
//     const checkboxes = document.querySelectorAll(`.multipleCB.${className}`);
//     const isChecked = element.checked;
//
//     checkboxes.forEach(checkbox => {
//         checkbox.checked = isChecked;
//     });
//
//     // Trigger the change event for the first checkbox (if exists)
//     if (checkboxes.length > 0) {
//         multiCBChanged(className, checkboxes[0]);
//     }
// }
//
// /**
//  * Document ready handler to initialize the UI
//  */
document.addEventListener('DOMContentLoaded', function () {
    // Initialize loading indicator
    const loadingIndicator = document.createElement('div');
    loadingIndicator.id = 'Tect-loading-indicator';
    loadingIndicator.className = 'Tect-loading-overlay';
    loadingIndicator.innerHTML = `

    `;
    document.body.appendChild(loadingIndicator);

    // Check for flash messages on page load
    if (typeof flashMessage !== 'undefined' && flashMessage.message) {
        showToast(flashMessage.message, flashMessage.type);
    }

    // Add class to parent body to ensure our styles don't conflict
    document.body.classList.add('Tect-workflow');
});

/**
 * Enhances the delivery dialog UI and functionality
 *
 * @param {HTMLElement} dialog - The dialog element
 * @param {string} deviceId - The device ID
 */
function enhanceDeliveryDialog(dialog, deviceId) {
    console.log("Enhancing delivery dialog for device:", deviceId);

    // Add workflow progress indicators at the top of the dialog
    addDeliveryProgressIndicator(dialog);

    // Find cases list container
    const casesContainer = dialog.querySelector('.Tect-cases-list') ||
        dialog.querySelector('.cases-list') ||
        dialog.querySelector('.table-responsive');

    if (!casesContainer) {
        console.error("Cases container not found in delivery dialog");
        return;
    }

    // Add enhanced styling class
    casesContainer.classList.add('delivery-cases-container');

    // Find all case items/rows
    const caseItems = casesContainer.querySelectorAll('tr') ||
        casesContainer.querySelectorAll('.case-item') ||
        casesContainer.querySelectorAll('.case-row');

    if (caseItems.length === 0) {
        console.log("No case items found in delivery dialog");
        return;
    }

    // Enhance each case item
    caseItems.forEach(item => {
        // Skip header rows
        if (item.tagName === 'TR' && item.querySelector('th')) {
            item.classList.add('delivery-header-row');
            return;
        }

        // Add selection feedback
        item.classList.add('delivery-case-item');

        // Find checkbox if it exists
        const checkbox = item.querySelector('input[type="checkbox"]');
        if (checkbox) {
            // Add event listener for selection feedback
            checkbox.addEventListener('change', function () {
                if (this.checked) {
                    item.classList.add('selected');
                } else {
                    item.classList.remove('selected');
                }

                // Update action buttons state
                updateDeliveryActionButtons(dialog);
            });
        }

        // Add click event to the entire row to toggle checkbox selection
        item.addEventListener('click', function (e) {
            // Skip if clicking on checkbox itself or action buttons
            if (e.target.type === 'checkbox' || e.target.tagName === 'BUTTON' ||
                e.target.closest('button') || e.target.closest('a')) {
                return;
            }

            const rowCheckbox = this.querySelector('input[type="checkbox"]');
            if (rowCheckbox) {
                rowCheckbox.checked = !rowCheckbox.checked;

                // Trigger change event to update UI
                const changeEvent = new Event('change');
                rowCheckbox.dispatchEvent(changeEvent);
            }
        });
    });

    // Set up workflow action buttons
    setupDeliveryActionButtons(dialog, deviceId);
}

/**
 * Adds a visual progress indicator to the delivery dialog
 *
 * @param {HTMLElement} dialog - The dialog element
 */
function addDeliveryProgressIndicator(dialog) {
    // Check if dialog already has progress indicator
    if (dialog.querySelector('.delivery-progress')) {
        return;
    }

    // Create progress indicator container
    const progressContainer = document.createElement('div');
    progressContainer.className = 'delivery-progress';

    // Create steps
    const steps = [
        {id: 'assign', label: 'Assign', number: 1},
        {id: 'start', label: 'Start', number: 2},
        {id: 'deliver', label: 'Deliver', number: 3}
    ];

    // Determine current stage
    let currentStage = 0;

    // Check if any cases in the dialog are already assigned or started
    const assignedCases = dialog.querySelectorAll('.case-assigned, .case-status-assigned, [data-status="assigned"]');
    const startedCases = dialog.querySelectorAll('.case-started, .case-status-started, [data-status="started"]');
    const deliveredCases = dialog.querySelectorAll('.case-delivered, .case-status-delivered, [data-status="delivered"]');

    if (deliveredCases.length > 0) {
        currentStage = 3; // Deliver stage
    } else if (startedCases.length > 0) {
        currentStage = 2; // Start stage
    } else if (assignedCases.length > 0) {
        currentStage = 1; // Assign stage
    }

    // Create each step in the progress indicator
    steps.forEach((step, index) => {
        const stepElement = document.createElement('div');
        stepElement.className = 'delivery-step';

        // Set active and completed classes based on current stage
        if (index + 1 < currentStage) {
            stepElement.classList.add('completed');
        } else if (index + 1 === currentStage) {
            stepElement.classList.add('active');
        }

        // Create step circle with number
        const circle = document.createElement('div');
        circle.className = 'delivery-step-circle';
        circle.textContent = step.number;

        // Create step label
        const label = document.createElement('div');
        label.className = 'delivery-step-label';
        label.textContent = step.label;

        // Add circle and label to step
        stepElement.appendChild(circle);
        stepElement.appendChild(label);

        // Add step to progress container
        progressContainer.appendChild(stepElement);
    });

    // Add progress indicator to dialog
    const dialogContent = dialog.querySelector('.Tect-workflow-dialog') || dialog.querySelector('.modal-content');
    if (dialogContent) {
        // Add after header but before body content
        const header = dialogContent.querySelector('.Tect-workflow-header') || dialogContent.querySelector('.modal-header');
        if (header && header.nextSibling) {
            dialogContent.insertBefore(progressContainer, header.nextSibling);
        } else {
            dialogContent.appendChild(progressContainer);
        }
    }
}

/**
 * Sets up the delivery workflow action buttons (Assign, Start, Deliver)
 *
 * @param {HTMLElement} dialog - The dialog element
 * @param {string} deviceId - The device ID
 */
function setupDeliveryActionButtons(dialog, deviceId) {
    // Find the action buttons container or create one if it doesn't exist
    let actionsContainer = dialog.querySelector('.delivery-actions');

    if (!actionsContainer) {
        // Find footer or create one
        let footer = dialog.querySelector('.modal-footer') || dialog.querySelector('.Tect-workflow-footer');

        if (!footer) {
            footer = document.createElement('div');
            footer.className = 'Tect-workflow-footer';
            const dialogContent = dialog.querySelector('.Tect-workflow-dialog') || dialog.querySelector('.modal-content');
            if (dialogContent) {
                dialogContent.appendChild(footer);
            }
        }

        // Create actions container
        actionsContainer = document.createElement('div');
        actionsContainer.className = 'delivery-actions';
        footer.appendChild(actionsContainer);

        // Create workflow buttons
        const assignButton = document.createElement('button');
        assignButton.id = `assign-button-${deviceId}`;
        assignButton.className = 'Tect-button delivery-action-btn';
        assignButton.textContent = 'Assign';
        assignButton.disabled = true;
        assignButton.onclick = () => processDeliveryAction(deviceId, 'assign');

        const startButton = document.createElement('button');
        startButton.id = `start-button-${deviceId}`;
        startButton.className = 'Tect-button Tect-button-success delivery-action-btn';
        startButton.textContent = 'Start';
        startButton.disabled = true;
        startButton.onclick = () => processDeliveryAction(deviceId, 'start');

        const deliverButton = document.createElement('button');
        deliverButton.id = `deliver-button-${deviceId}`;
        deliverButton.className = 'Tect-button Tect-button-orange delivery-action-btn';
        deliverButton.textContent = 'Deliver';
        deliverButton.disabled = true;
        deliverButton.onclick = () => processDeliveryAction(deviceId, 'deliver');

        // Add buttons to container
        actionsContainer.appendChild(assignButton);
        actionsContainer.appendChild(startButton);
        actionsContainer.appendChild(deliverButton);
    }
}

/**
 * Updates the state of delivery action buttons based on selected cases
 *
 * @param {HTMLElement} dialog - The dialog element
 */
function updateDeliveryActionButtons(dialog) {
    // Find all action buttons
    const actionButtons = dialog.querySelectorAll('.delivery-action-btn');

    // Get selected case items
    selectedItems = dialog.querySelectorAll('.delivery-case-item.selected');
    const hasSelection = selectedItems.length > 0;

    // Enable/disable buttons based on selection
    actionButtons.forEach(button => {
        button.disabled = !hasSelection;
    });
}

/**
 * Process a delivery workflow action (assign, start, deliver)
 *
 * @param {string} deviceId - The device ID
 * @param {string} action - The action to perform ('assign', 'start', 'deliver')
 */
function processDeliveryAction(deviceId, action) {
    // Show loading indicator
    showLoadingIndicator();

    // Get selected case IDs
    const selectedCases = [];
    const dialog = document.getElementById(`${deviceId}casesListDialog`) ||
        document.getElementById(`${deviceId}-cases-dialog`) ||
        document.getElementById(`device-${deviceId}-dialog`);

    if (dialog) {
        const checkboxes = dialog.querySelectorAll('.delivery-case-item.selected input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            if (checkbox.value) {
                selectedCases.push(checkbox.value);
            }
        });
    }

    if (selectedCases.length === 0) {
        hideLoadingIndicator();
        showToast('Please select at least one case', 'warning');
        return;
    }

    console.log(`Processing delivery action: ${action} for device: ${deviceId}, cases:`, selectedCases);

    // Set up form data based on action
    const form = document.getElementById(`delivery-form-${deviceId}`);
    if (!form) {
        // Create form if it doesn't exist
        const newForm = document.createElement('form');
        newForm.id = `delivery-form-${deviceId}`;
        newForm.method = 'POST';
        newForm.style.display = 'none';

        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.content;
            newForm.appendChild(csrfInput);
        }

        // Add device ID
        const deviceInput = document.createElement('input');
        deviceInput.type = 'hidden';
        deviceInput.name = 'deviceId';
        deviceInput.value = deviceId;
        newForm.appendChild(deviceInput);

        // Add cases input
        const casesInput = document.createElement('input');
        casesInput.type = 'hidden';
        casesInput.name = 'cases';
        casesInput.value = selectedCases.join(',');
        newForm.appendChild(casesInput);

        // Add action input
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = action;
        newForm.appendChild(actionInput);

        // Set action URL based on action type
        if (action === 'assign') {
            newForm.action = '/assign-to-delivery-person';
        } else if (action === 'start') {
            newForm.action = '/start-delivery';
        } else if (action === 'deliver') {
            newForm.action = '/complete-delivery';
        }

        // Add form to document
        document.body.appendChild(newForm);

        // Submit form
        newForm.submit();
    } else {
        // Update existing form
        const casesInput = form.querySelector('input[name="cases"]');
        if (casesInput) {
            casesInput.value = selectedCases.join(',');
        }

        const actionInput = form.querySelector('input[name="action"]');
        if (actionInput) {
            actionInput.value = action;
        }

        // Set action URL based on action type
        if (action === 'assign') {
            form.action = '/assign-to-delivery-person';
        } else if (action === 'start') {
            form.action = '/start-delivery';
        } else if (action === 'deliver') {
            form.action = '/complete-delivery';
        }

        // Submit form
        form.submit();
    }
}


/**
 * Handle selection of a delivery driver from the grid
 *
 * @param {HTMLElement} element - The driver card that was clicked
 * @param {number} driverId - The ID of the selected driver
 */
function selectDeliveryDriver(element, driverId) {
    console.log('Selecting delivery driver:', driverId);

    // Remove selection from all cards
    document.querySelectorAll('.Tect-driver-card').forEach(card => {
        card.classList.remove('first-time');
        card.classList.remove('selected');

        // Add grayscale to all images
        const img = card.querySelector('.Tect-driver-image');
        if (img) {
            img.classList.add('grayscale');
        }
    });

    // Get the assign button
    const assignButton = document.getElementById('action-button-delivery');

    // Always select the clicked driver (don't toggle)
    // Select this driver
    element.classList.add('selected');

    // Remove grayscale from the selected driver's image
    const img = element.querySelector('.Tect-driver-image');
    if (img) {
        img.classList.remove('grayscale');
    }

    // Store the selected driver ID
    window.selectedDriverId = driverId;

    // Update the hidden form field
    const driverIdInput = document.getElementById('driver-id-input');
    if (driverIdInput) {
        driverIdInput.value = driverId;
    }

    // Enable the assign button
    if (assignButton) {
        assignButton.disabled = false;
    }

    console.log('Selected driver ID:', window.selectedDriverId);
}

/**
 * Submit the delivery assignment form
 */
function submitDeliveryAssignment() {
    console.log('Submitting delivery assignment');

    // Get the selected driver ID
    if (!window.selectedDriverId) {
        showToast('Please select a driver', 'warning');
        return;
    }

    // Get the selected case IDs - use our helper function that captures checked checkboxes
    // in the waiting delivery tables
    const deliveryCheckboxes = document.querySelectorAll('input[name="WaitingPopupCheckBoxesdelivery"]:checked');
    let caseIds = [];

    deliveryCheckboxes.forEach(checkbox => {
        if (checkbox.value) {
            caseIds.push(checkbox.value);
        }
    });

    // If no checkboxes found with that name, try fallback method
    if (caseIds.length === 0) {
        caseIds = getCheckedValues('delivery') || [];
    }

    // Check if we have any cases selected
    if (caseIds.length === 0) {
        showToast('Please select at least one case', 'warning');
        return;
    }

    console.log('Assigning cases to driver:', window.selectedDriverId, 'Case IDs:', caseIds);

    // Update the form fields
    const driverIdInput = document.getElementById('driver-id-input');
    const caseIdsInput = document.getElementById('case-ids-input');

    if (driverIdInput && caseIdsInput) {
        driverIdInput.value = window.selectedDriverId;
        caseIdsInput.value = caseIds.join(',');

        // Show loading indicator
        showLoadingIndicator();

        // Submit the form
        const form = document.getElementById('delivery-form');
        if (form) {
            console.log('Submitting form with driver ID:', driverIdInput.value, 'and case IDs:', caseIdsInput.value);
            form.submit();
        } else {
            hideLoadingIndicator();
            showToast('Error: Form not found', 'error');
        }
    } else {
        showToast('Error: Form fields not found', 'error');
    }
}

// Add this to your existing DOMContentLoaded event
document.addEventListener('DOMContentLoaded', function () {
    // Initialize driver cards - don't set them as selected by default
    document.querySelectorAll('.Tect-driver-card').forEach(card => {
        // Just ensure they have grayscale images initially
        const img = card.querySelector('.Tect-driver-image');
        if (img) {
            img.classList.add('grayscale');
        }
    });
});


/**
 * THE FIIIIIIIIIIIINAAAAAAAAAAAALLLL COUNNNNTDOWN
 *
 * @param {string} deviceId - The device ID
 * @param {string} type - The workflow type/stage
 */

function getSelectedJobIds() {
    let jobIds = [];

    // If any blue is selected, only take blue ones
    const $blueChecked = $('.active-blue-row:checked');
    if ($blueChecked.length > 0) {
        $blueChecked.each(function () {
            jobIds.push(this.value);
        });
    } else {
        // Otherwise take selected orange ones
        $('.inactive-orange-row:checked').each(function () {
            jobIds.push(this.value);
        });
    }

    return jobIds;
}

/**
 * Get checked values from various checkbox selectors based on type
 *
 * @param {string} type - The workflow type/stage
 * @returns {Array} - Array of case IDs
 */
function getCheckedValues(type) {
    console.log(`Getting checked values for type: ${type}`);

    // For delivery type, check all possible checkbox classes and names
    if (type === 'delivery') {
        // Try all possible selectors for delivery checkboxes
        let checkboxSelectors = [
            `.multipleCB.${type}:checked`,
            `input[name="WaitingPopupCheckBoxesdelivery"]:checked`,
            `.delivery-checkbox:checked`,
            `.delivery-case-checkbox:checked`,
            `input[type="checkbox"][name*="delivery"]:checked`,
            `tr.selected input[type="checkbox"]`
        ];

        // Go through each selector and try to find checkboxes
        for (const selector of checkboxSelectors) {
            const checkboxes = document.querySelectorAll(selector);
            if (checkboxes.length > 0) {
                console.log(`Found ${checkboxes.length} checked delivery checkboxes with selector: ${selector}`);
                return Array.from(checkboxes).map(cb => cb.value);
            }
        }

        // If still no checkboxes found, look for selected rows that might have data attributes
        const selectedRows = document.querySelectorAll('.delivery-case-item.selected');
        if (selectedRows.length > 0) {
            console.log(`Found ${selectedRows.length} selected delivery rows`);
            return Array.from(selectedRows).map(row => {
                // Try to get case ID from data attribute or input
                return row.dataset.caseId ||
                    row.querySelector('input[type="checkbox"]')?.value ||
                    row.id.replace('case-row-', '');
            }).filter(id => id);
        }

        console.warn('No delivery checkboxes found with any selector');
        return [];
    }

    // Fallback to standard checkbox class for other types
    const checkboxes = document.querySelectorAll(`.multipleCB.${CSS.escape(type)}:checked`);
    if (checkboxes.length > 0) {
        return Array.from(checkboxes).map(cb => cb.value);
    }

    // Try generic selector as last resort
    const genericCheckboxes = document.querySelectorAll(`input[type="checkbox"][name*="${type}"]:checked`);
    return Array.from(genericCheckboxes).map(cb => cb.value);
}


/**
 * Update action button state based on selection
 *
 * @param {string} deviceId - The device ID
 * @param {string} type - The workflow type/stage
 */
function updateActionXXXXXButtonState(deviceId, type) {
    type = type.startsWith('3') ? '\\33 ' + type.slice(1) : type;
    const selector = '.Tect-machine-card.' + (type);

    const checkboxes = document.querySelectorAll(`.Tect-job-row input[type="checkbox"]`);
    const actionButton = document.getElementById(`actionX-button-${deviceId}`);

    // Check if any jobs are already active
    const activeJobs = document.querySelectorAll(type + ` .Tect-job-row[style*="--main-blue"]`);

    if (activeJobs.length > 0) {
        // If there are active jobs, only show the complete button
        actionButton.textContent = 'COMPLETE';
        actionButton.style.backgroundColor = 'var(--main-green)';

        // Disable all checkboxes as we can't start new jobs while one is active
        checkboxes.forEach(checkbox => {
            checkbox.disabled = true;
        });

        return;
    }

    // Count selected items
    let selectedCount = 0;
    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            selectedCount++;
        }
    });

    // Update button state
    if (selectedCount > 0) {
        actionButton.textContent = 'START';
        actionButton.style.backgroundColor = 'var(--main-blue)';
        actionButton.disabled = false;
    } else {
        actionButton.disabled = true;
    }
}
