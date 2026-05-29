@props(['title', 'btnText', 'type', 'employees', 'stageId', 'stageName'])

<div class="Albasma-workflow-modal waiting-dialog" id="EmployeeDialog{{ $type }}" tabindex="-1" role="dialog">
    <div class="Albasma-workflow-dialog">
        <!-- Header with close button -->
        <div class="Albasma-workflow-header">
            <h2 class="Albasma-workflow-title">{{ $title }}</h2>
            <button class="Albasma-close-button" onclick="closeEmployeeModal('{{ $type }}')">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <!-- Employee selection grid -->
        <div class="Albasma-workflow-body">
            <div class="Albasma-drivers-grid">
                <!-- Show all employees with permission for this stage -->
                @foreach($employees as $employee)
                    <div class="Albasma-driver-card"
                         onclick="selectEmployee('{{ $type }}', this, {{ $employee->id }})">
                        <div class="Albasma-driver-image-container">
                            <img src="{{ $employee->has_photo ? asset('/users/'.$employee->id.'/profile_picture.png') : asset('/users/no_profile_picture.png') }}"
                                 alt="{{ $employee->first_name }} {{ $employee->last_name }}"
                                 class="Albasma-driver-image grayscale">
                        </div>
                        <div class="Albasma-driver-name">{{ $employee->name_initials ?? $employee->first_name }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Action button -->
        <div class="Albasma-workflow-footer">
            <button type="button"
                    class="Albasma-button"
                    id="action-button-{{ $type }}-employee"
                    style="background-color: var(--main-orange)"
                    disabled
                    onclick="submitEmployeeAssignment('{{ $type }}')">
                {{ $btnText }}
            </button>
        </div>
    </div>
</div>

<form id="employee-form-{{ $type }}" method="POST" action="{{ route('assign-multiple-employees') }}" class="d-none">
    @csrf
    <input type="hidden" name="type" value="{{ $type }}">
    <input type="hidden" name="employeeId-{{ $type }}" id="employee-id-input-{{ $type }}" value="">
    <input type="hidden" name="WaitingPopupCheckBoxes{{ $type }}" id="case-ids-input-{{ $type }}" value="">
</form>

<script>
// Track selected employees per stage type
window.selectedEmployeeIds = window.selectedEmployeeIds || {};

/**
 * Select an employee for assignment
 */
function selectEmployee(type, cardElement, employeeId) {
    // Remove selection from all cards in this dialog
    const dialog = document.getElementById('EmployeeDialog' + type);
    if (!dialog) return;

    dialog.querySelectorAll('.Albasma-driver-card').forEach(card => {
        card.classList.remove('selected');
        const img = card.querySelector('.Albasma-driver-image');
        if (img) {
            img.classList.add('grayscale');
        }
    });

    // Select the clicked card
    cardElement.classList.add('selected');
    const img = cardElement.querySelector('.Albasma-driver-image');
    if (img) {
        img.classList.remove('grayscale');
    }

    // Store selected employee ID
    window.selectedEmployeeIds[type] = employeeId;

    // Update hidden input
    const employeeInput = document.getElementById('employee-id-input-' + type);
    if (employeeInput) {
        employeeInput.value = employeeId;
    }

    // Enable assign button
    const assignButton = document.getElementById('action-button-' + type + '-employee');
    if (assignButton) {
        assignButton.disabled = false;
    }

    console.log('Selected employee ' + employeeId + ' for ' + type);
}

/**
 * Submit employee assignment form
 */
function submitEmployeeAssignment(type) {
    const form = document.getElementById('employee-form-' + type);
    const assignButton = document.getElementById('action-button-' + type + '-employee');

    if (!form) {
        console.error('Employee form not found for type: ' + type);
        return;
    }

    // Check if employee is selected
    const employeeIdInput = document.getElementById('employee-id-input-' + type);
    if (!employeeIdInput || !employeeIdInput.value) {
        alert('Please select an employee');
        return;
    }

    // Get selected case IDs from checkboxes
    const checkboxes = document.querySelectorAll(`input[data-stage-type="${type}"][data-case-checkbox]:checked`);
    if (checkboxes.length === 0) {
        alert('No cases selected');
        return;
    }

    const caseIds = Array.from(checkboxes).map(cb => cb.getAttribute('data-case-id')).join(',');
    const caseIdsInput = document.getElementById('case-ids-input-' + type);
    if (caseIdsInput) {
        caseIdsInput.value = caseIds;
    }

    // Show loading state
    if (assignButton) {
        assignButton.disabled = true;
        assignButton.classList.add('btn-loading');
        assignButton.innerText = 'ASSIGNING...';
    }

    console.log('Submitting employee assignment:', {
        type: type,
        employeeId: employeeIdInput.value,
        caseIds: caseIds
    });

    // Submit the form
    form.submit();
}

/**
 * Close employee modal
 */
function closeEmployeeModal(type) {
    const modalId = 'EmployeeDialog' + type;
    const modal = document.getElementById(modalId);

    if (!modal) {
        console.error(`Modal not found: ${modalId}`);
        return;
    }

    console.log(`Closing employee modal: ${modalId}`);

    // Remove focus if modal contains active element
    if (modal.contains(document.activeElement)) {
        document.activeElement.blur();
    }

    // Clear any pending animations
    const dialogContent = modal.querySelector('.Albasma-workflow-dialog');
    if (dialogContent) {
        dialogContent.classList.remove('fade-in');
        dialogContent.classList.add('fade-out');
    }

    // Reset employee selection
    modal.querySelectorAll('.Albasma-driver-card').forEach(card => {
        card.classList.remove('selected');
        const img = card.querySelector('.Albasma-driver-image');
        if (img) {
            img.classList.add('grayscale');
        }
    });

    // Reset the assign button state
    const assignButton = document.getElementById('action-button-' + type + '-employee');
    if (assignButton) {
        assignButton.disabled = true;
        assignButton.classList.remove('btn-loading', 'disabled');
        assignButton.innerText = 'ASSIGN';
    }

    // Clear selected employee
    if (window.selectedEmployeeIds) {
        window.selectedEmployeeIds[type] = null;
    }

    // Reset form inputs
    const employeeInput = document.getElementById('employee-id-input-' + type);
    const caseIdsInput = document.getElementById('case-ids-input-' + type);
    if (employeeInput) employeeInput.value = '';
    if (caseIdsInput) caseIdsInput.value = '';

    // Hide dialog after animation completes
    setTimeout(() => {
        modal.classList.remove('active');
        if (dialogContent) {
            dialogContent.classList.remove('fade-out', 'fade-in');
        }
    }, 300);
}

/**
 * Open employee modal
 */
function openEmployeeModal(type) {
    const modalId = 'EmployeeDialog' + type;
    const modal = document.getElementById(modalId);

    if (!modal) {
        console.error(`Modal not found: ${modalId}`);
        return;
    }

    // Check if any cases are selected
    const checkboxes = document.querySelectorAll(`input[data-stage-type="${type}"][data-case-checkbox]:checked`);
    if (checkboxes.length === 0) {
        alert('Please select at least one case');
        return;
    }

    console.log(`Opening employee modal: ${modalId}`);

    // Show modal
    modal.classList.add('active');

    // Animate dialog content
    const dialogContent = modal.querySelector('.Albasma-workflow-dialog');
    if (dialogContent) {
        dialogContent.classList.add('fade-in');
    }
}
</script>
