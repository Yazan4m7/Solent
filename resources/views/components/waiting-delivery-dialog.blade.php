@props(['title', 'btnText', 'type', 'drivers', 'stageId'])

<div class="alrazi-workflow-modal waiting-dialog" id="DeliveryDialog" tabindex="-1" role="dialog">
    <div class="alrazi-workflow-dialog">
        <!-- Header with close button -->
        <div class="alrazi-workflow-header">
            <h2 class="alrazi-workflow-title">{{ $title }}</h2>
            <button class="alrazi-close-button" onclick="closeModal({id: 'DeliveryDialog', isWaiting:false})">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <!-- Driver selection grid -->
        <div class="alrazi-workflow-body">
            <div class="alrazi-drivers-grid">
                <!-- Add "ME" option -->


                <!-- Show all delivery drivers -->
                @foreach($drivers as $driver)
                    <div class="alrazi-driver-card"
                         onclick="selectDeliveryDriver(this, {{ $driver->id }})">
                        <div class="alrazi-driver-image-container">
                            <img src="{{ $driver->has_photo ? asset('/users/'.$driver->id.'/profile_picture.png') : asset('/users/no_profile_picture.png') }}"
                                 alt="{{ $driver->first_name }} {{ $driver->last_name }}"
                                 class="alrazi-driver-image grayscale">
                        </div>
                        <div class="alrazi-driver-name">{{ $driver->name_initials ?? $driver->first_name }}</div>
                    </div>
                @endforeach
            </div>


        </div>

        <!-- Action button -->
        <div class="alrazi-workflow-footer">
            <button type="button"
                    class="alrazi-button "
                    id="action-button-delivery"
                    style="background-color: var(--main-orange)"
                    disabled
                    onclick="submitDeliveryAssignment()">
                {{ $btnText }}
            </button>
        </div>
    </div>
</div>

<form id="delivery-form" method="POST" action="{{ route('assign-multiple-deliveries') }}" class="d-none">
    @csrf
    <input type="hidden" name="deviceId-delivery" id="driver-id-input" value="">
    <input type="hidden" name="WaitingPopupCheckBoxesdelivery" id="case-ids-input" value="">
</form>

<script>
// Helper function to close the modal properly
function closeModal(options) {
    const {id, isWaiting = false} = options;
    const modalId = id + (isWaiting ? "-waiting" : "");
    const modal = document.getElementById(modalId);

    if (!modal) {
        console.error(`Modal not found: ${modalId}`);
        return;
    }

    console.log(`Closing delivery modal: ${modalId}`);

    // Remove focus if modal contains active element
    if (modal.contains(document.activeElement)) {
        document.activeElement.blur();
    }

    // Clear any pending animations
    const dialogContent = modal.querySelector('.alrazi-workflow-dialog') || modal.querySelector('.modal-content');
    if (dialogContent) {
        dialogContent.classList.remove('fade-in');
        dialogContent.classList.add('fade-out');
    }

    // Reset delivery dialog state
    if (modalId === 'DeliveryDialog') {
        // Reset driver selection
        document.querySelectorAll('.alrazi-driver-card').forEach(card => {
            card.classList.remove('selected');
            const img = card.querySelector('.alrazi-driver-image');
            if (img) {
                img.classList.add('grayscale');
            }
        });

        // Reset the assign button state
        const assignButton = document.getElementById('action-button-delivery');
        if (assignButton) {
            assignButton.disabled = true;
            assignButton.classList.remove('btn-loading', 'disabled');
            assignButton.innerText = 'ASSIGN';
        }

        // Clear selected driver
        window.selectedDriverId = null;

        // Reset form inputs
        const driverInput = document.getElementById('driver-id-input');
        const caseIdsInput = document.getElementById('case-ids-input');
        if (driverInput) driverInput.value = '';
        if (caseIdsInput) caseIdsInput.value = '';
    }

    // Hide dialog after animation completes
    setTimeout(() => {
        modal.classList.remove('active');
        if (dialogContent) {
            dialogContent.classList.remove('fade-out', 'fade-in');
        }

        // Clean up any overlays
        document.querySelectorAll('.modal-backdrop, .modal-overlay').forEach(backdrop => {
            backdrop.remove();
        });
    }, 300);
}
</script>
