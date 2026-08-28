<?php

/**
 * UserImagePicker Component
 *
 * A component for selecting and previewing user profile images
 * Only accepts PNG files, max size 2MB
 */

$id = 'user-image-picker-' . uniqid();
$current_image = $attributes['current_image'] ?? null;
?>

<div class="user-image-picker-container">
    <div class="row">
        <div class="col-md-6 input-container">
            <div class="form-group">
                <label for="<?php echo $id; ?>">Profile Image (PNG only, max 2MB)</label>
                <input type="file"
                       class="form-control-file user-image-input"
                       id="<?php echo $id; ?>"
                       name="photo"
                       accept=".png" >
                <small class="form-text text-muted">Only PNG files are accepted. Maximum file size: 2MB, Click this box to pick a photo</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="image-preview-container text-center">
                <div class="loading-indicator" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p>Uploading image...</p>
                </div>
                <div class="image-preview">
                    <?php if ($current_image): ?>
                        <img src="<?php echo $current_image; ?>" alt="Profile image" class="img-fluid rounded" style="max-height: 150px;">
                    <?php else: ?>
                        <div class="user-image-placeholder" aria-label="Default profile image">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                <circle cx="12" cy="8" r="4"></circle>
                                <path d="M4.5 20c.7-4 3.2-6 7.5-6s6.8 2 7.5 6"></path>
                            </svg>
                            <span>User's profile</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.user-image-picker-container {
    margin-bottom: 0;
}

.user-image-picker-container > .row {
    display: grid;
    gap: 16px;
    grid-template-columns: minmax(0, 1.35fr) minmax(220px, .65fr);
    margin: 0;
}

.user-image-picker-container > .row > [class*="col-"] {
    flex: none;
    max-width: none;
    padding: 0;
    width: auto;
}

.user-image-picker-container .input-container,
.user-image-picker-container .image-preview-container {
    align-items: center;
    background: var(--surface-raised, #f8fafc);
    border: 1px dashed #cbd5e1;
    border-radius: 12px;
    display: flex;
    min-height: 168px;
    padding: 20px;
}

.user-image-picker-container .input-container .form-group {
    margin: 0;
    width: 100%;
}

.user-image-picker-container .user-image-input {
    background: var(--surface, #ffffff);
    border: 1px solid #dbe3eb;
    border-radius: 9px;
    cursor: pointer;
    height: auto;
    margin-top: 8px;
    padding: 10px;
    width: 100%;
}

.user-image-picker-container .image-preview-container {
    justify-content: center;
    margin-top: 0;
    text-align: center;
}

.user-image-picker-container .loading-indicator {
    text-align: center;
}

.user-image-picker-container .user-image-placeholder {
    align-items: center;
    color: var(--text-2, #64748b);
    display: flex;
    flex-direction: column;
    font-size: 13px;
    font-weight: 650;
    gap: 8px;
}

.user-image-picker-container .user-image-placeholder svg {
    fill: none;
    height: 54px;
    stroke: var(--accent, #6366f1);
    stroke-linecap: round;
    stroke-linejoin: round;
    stroke-width: 1.5;
    width: 54px;
}

@media (max-width: 767.98px) {
    .user-image-picker-container > .row {
        grid-template-columns: minmax(0, 1fr);
    }

    .user-image-picker-container .input-container,
    .user-image-picker-container .image-preview-container {
        min-height: 140px;
        padding: 16px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('<?php echo $id; ?>');
    const previewContainer = fileInput.closest('.user-image-picker-container').querySelector('.image-preview');
    const loadingIndicator = fileInput.closest('.user-image-picker-container').querySelector('.loading-indicator');

    fileInput.addEventListener('change', function() {
        const file = this.files[0];

        if (file) {
            // Check file type
            if (file.type !== 'image/png') {
                alert('Only PNG files are allowed');
                this.value = '';
                return;
            }

            // Check file size (2MB = 512000 bytes)
            if (file.size >2048000) {
                alert('Brooo, file size must be less than 2MB, bruuh');
                this.value = '';
                return;
            }

            // Show loading indicator
            previewContainer.style.display = 'none';
            loadingIndicator.style.display = 'block';

            const reader = new FileReader();

            reader.onload = function(e) {
                // Simulate loading delay (3 seconds)
                setTimeout(function() {
                    // Hide loading indicator
                    loadingIndicator.style.display = 'none';
                    previewContainer.style.display = 'block';

                    // Update preview image
                    const img = previewContainer.querySelector('img') || document.createElement('img');
                    img.src = e.target.result;
                    img.alt = 'Selected profile image';
                    img.className = 'img-fluid rounded';
                    img.style.maxHeight = '150px';

                    if (!previewContainer.querySelector('img')) {
                        previewContainer.appendChild(img);
                    }
                }, 3000);
            };

            reader.readAsDataURL(file);
        }
    });
});
</script>
