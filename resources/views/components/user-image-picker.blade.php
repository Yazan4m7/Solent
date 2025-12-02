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
                        <img src="/assets/images/default-avatar.png" alt="Default profile image"  onerror="
    // Stop further error handling (avoid loops)
    this.onerror = null;

    // Hide the broken image
    this.style.display = 'none';

    // Show a separate text span
    document.getElementById('profile-fallback').style.display = 'inline';
  " class="img-fluid rounded" style="max-height: 150px;">
                        <span id="profile-fallback" style="display:none;">
  User's profile
</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>

    .input-container{

        background-color: #f8f9fa;
        border: #c3c3c3 solid 2px;
        border-radius: 5px;

    }
    .user-image-input{
        height: 12rem;
        cursor: pointer;
    }
.user-image-picker-container {
    margin-bottom: 50px;
}
.image-preview-container {
    margin-top: 10px;
    padding: 15px;
    border: 1px dashed #ccc;
    border-radius: 5px;
    background-color: #f8f9fa;
    min-height: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.loading-indicator {
    text-align: center;
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
