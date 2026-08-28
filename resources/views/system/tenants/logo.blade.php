@extends('layouts.app', ['pageSlug' => 'Edit Tenant Logo', 'platformAdminPage' => true])

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/intel-dashboard-css/formplugins/cropperjs/cropper.css') }}">

    <style>
        .tenant-logo-page {
            max-width: 920px;
            margin: 0 auto;
        }

        .tenant-logo-card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
        }

        .tenant-logo-card__body {
            display: grid;
            gap: 22px;
            padding: 28px;
        }

        .tenant-logo-option {
            align-items: center;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            display: grid;
            gap: 24px;
            grid-template-columns: minmax(180px, 230px) minmax(0, 1fr);
            padding: 20px;
        }

        .tenant-logo-option h2 {
            font-size: 18px;
            margin: 0 0 6px;
        }

        .tenant-logo-option p {
            color: #64748b;
            font-size: 13px;
            margin: 0 0 14px;
        }

        .tenant-logo-preview {
            align-items: center;
            background: #10182c;
            border: 1px solid #273451;
            border-radius: 14px;
            display: flex;
            justify-content: center;
            min-height: 180px;
            padding: 22px;
        }

        .tenant-logo-preview img {
            display: block;
            max-height: 164px;
            max-width: 100%;
            object-fit: contain;
            filter: brightness(4);
        }

        .tenant-logo-upload {
            align-items: center;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            min-height: 52px;
            padding: 10px 14px;
        }

        .tenant-logo-upload input {
            width: 100%;
        }

        .tenant-logo-help {
            color: #64748b;
            font-size: 13px;
            line-height: 1.55;
            margin-top: 9px;
        }

        .tenant-logo-crop-button {
            align-items: center;
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            border-radius: 9px;
            color: #4338ca;
            display: none;
            font-size: 12px;
            font-weight: 700;
            gap: 7px;
            margin-top: 10px;
            min-height: 38px;
            padding: 8px 12px;
        }

        .tenant-logo-crop-button.is-visible {
            display: inline-flex;
        }

        .tenant-logo-required-note {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            color: #1e3a8a;
            font-size: 13px;
            padding: 10px 12px;
        }

        .tenant-logo-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 24px;
        }

        .tenant-logo-crop-modal[hidden] {
            display: none !important;
        }

        .tenant-logo-crop-modal {
            align-items: center;
            background: rgba(15, 23, 42, .72);
            display: flex;
            inset: 0;
            justify-content: center;
            padding: 18px;
            position: fixed;
            z-index: 1100;
        }

        .tenant-logo-crop-dialog {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .3);
            display: flex;
            flex-direction: column;
            max-height: calc(100vh - 36px);
            max-width: 840px;
            overflow: hidden;
            width: 100%;
        }

        .tenant-logo-crop-header {
            align-items: flex-start;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            gap: 18px;
            padding: 17px 20px;
        }

        .tenant-logo-crop-header h2 {
            color: #0f172a;
            font-size: 18px;
            font-weight: 800;
            margin: 0;
        }

        .tenant-logo-crop-header p {
            color: #64748b;
            font-size: 12px;
            line-height: 1.5;
            margin: 4px 0 0;
        }

        .tenant-logo-crop-close {
            align-items: center;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            color: #475569;
            display: inline-flex;
            flex: 0 0 40px;
            font-size: 22px;
            height: 40px;
            justify-content: center;
            line-height: 1;
            padding: 0;
        }

        .tenant-logo-crop-stage {
            background: #0f172a;
            flex: 1 1 auto;
            min-height: 300px;
            overflow: hidden;
        }

        .tenant-logo-crop-stage img {
            display: block;
            filter: none !important;
            max-width: 100%;
        }

        .tenant-logo-crop-error {
            background: #fef2f2;
            border-top: 1px solid #fecaca;
            color: #b91c1c;
            display: none;
            font-size: 12px;
            padding: 9px 18px;
        }

        .tenant-logo-crop-error.is-visible {
            display: block;
        }

        .tenant-logo-crop-actions {
            align-items: center;
            background: #fff;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 9px;
            justify-content: flex-end;
            padding: 14px 18px;
        }

        .tenant-logo-crop-actions .btn {
            min-height: 42px;
        }

        @media (max-width: 680px) {
            .tenant-logo-card__body {
                padding: 20px;
            }

            .tenant-logo-option {
                grid-template-columns: 1fr;
                padding: 16px;
            }

            .tenant-logo-preview {
                min-height: 180px;
            }

            .tenant-logo-actions {
                flex-direction: column-reverse;
            }

            .tenant-logo-actions .btn {
                min-height: 46px;
                width: 100%;
            }

            .tenant-logo-crop-modal {
                align-items: stretch;
                padding: 0;
            }

            .tenant-logo-crop-dialog {
                border-radius: 0;
                max-height: 100vh;
                min-height: 100vh;
            }

            .tenant-logo-crop-stage {
                min-height: 0;
            }

            .tenant-logo-crop-actions {
                display: grid;
                grid-template-columns: 1fr 1fr;
                padding: 12px;
            }

            .tenant-logo-crop-actions .btn-primary {
                grid-column: 1 / -1;
                grid-row: 1;
            }
        }
    </style>

    <div class="container-fluid py-3 tenant-logo-page">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h1 class="h3 mb-1">Edit tenant logos</h1>
                <div class="text-muted">{{ $tenant->name }} · {{ optional($tenant->primaryDomain)->host }}</div>
            </div>
            <a href="{{ route('system.tenants.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>

        <div class="card tenant-logo-card">
            <form method="POST" action="{{ route('system.tenants.logo.update', $tenant) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="tenant-logo-card__body">
                    <div class="tenant-logo-required-note">Both files are required when saving. Each logo is stored and used independently.</div>

                    <section class="tenant-logo-option" data-logo-field>
                        <div class="tenant-logo-preview">
                            <img src="{{ asset($currentLoginLogoPath) }}" alt="Current {{ $tenant->name }} login logo" data-logo-preview-image data-logo-fallback="{{ asset(config('branding.defaults.login_logo_path')) }}">
                        </div>
                        <div>
                            <h2>Login screen logo</h2>
                            <p>Displayed above the tenant sign-in form.</p>
                            <label class="tenant-logo-upload" for="tenant-login-logo">
                                <input id="tenant-login-logo" name="login_logo" type="file" accept="image/png,image/jpeg,image/webp" required data-logo-input>
                            </label>
                            <button type="button" class="tenant-logo-crop-button" data-logo-adjust-crop>Adjust crop</button>
                            @error('login_logo')
                                <div class="text-danger mt-2" role="alert">{{ $message }}</div>
                            @enderror
                            <div class="tenant-logo-help">PNG, JPG, or WebP. Maximum 5 MB. Between 64×64 and 4096×4096 pixels.</div>
                        </div>
                    </section>

                    <section class="tenant-logo-option" data-logo-field>
                        <div class="tenant-logo-preview">
                            <img src="{{ asset($currentSidebarLogoPath) }}" alt="Current {{ $tenant->name }} sidebar logo" data-logo-preview-image data-logo-fallback="{{ asset(config('branding.defaults.sidebar_mark_path')) }}">
                        </div>
                        <div>
                            <h2>Sidebar logo</h2>
                            <p>Displayed at the top of the tenant side menu.</p>
                            <label class="tenant-logo-upload" for="tenant-sidebar-logo">
                                <input id="tenant-sidebar-logo" name="sidebar_logo" type="file" accept="image/png,image/jpeg,image/webp" required data-logo-input>
                            </label>
                            <button type="button" class="tenant-logo-crop-button" data-logo-adjust-crop>Adjust crop</button>
                            @error('sidebar_logo')
                                <div class="text-danger mt-2" role="alert">{{ $message }}</div>
                            @enderror
                            <div class="tenant-logo-help">PNG, JPG, or WebP. Maximum 5 MB. Between 64×64 and 4096×4096 pixels.</div>
                        </div>
                    </section>

                    <div class="tenant-logo-actions">
                        <a href="{{ route('system.tenants.show', $tenant) }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save both logos</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="tenant-logo-crop-modal" data-logo-crop-modal hidden>
        <div class="tenant-logo-crop-dialog" role="dialog" aria-modal="true" aria-labelledby="tenant-logo-crop-title">
            <div class="tenant-logo-crop-header">
                <div>
                    <h2 id="tenant-logo-crop-title">Crop logo</h2>
                    <p>Drag and resize the box freely to keep exactly the icon, text, or both.</p>
                </div>
                <button type="button" class="tenant-logo-crop-close" data-logo-crop-cancel aria-label="Close crop editor">×</button>
            </div>
            <div class="tenant-logo-crop-stage">
                <img src="" alt="Selected logo crop preview" data-logo-crop-image>
            </div>
            <div class="tenant-logo-crop-error" data-logo-crop-error role="alert"></div>
            <div class="tenant-logo-crop-actions">
                <button type="button" class="btn btn-outline-secondary" data-logo-crop-cancel>Cancel</button>
                <button type="button" class="btn btn-outline-primary" data-logo-use-full>Use full image</button>
                <button type="button" class="btn btn-primary" data-logo-apply-crop>Apply crop</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/intel-dashboard-js/formplugins/cropperjs/cropper.js') }}"></script>
    <script>
        (function () {
            const modal = document.querySelector('[data-logo-crop-modal]');
            const cropImage = document.querySelector('[data-logo-crop-image]');
            const cropError = document.querySelector('[data-logo-crop-error]');
            const applyButton = document.querySelector('[data-logo-apply-crop]');
            const useFullButton = document.querySelector('[data-logo-use-full]');
            const cancelButtons = document.querySelectorAll('[data-logo-crop-cancel]');
            let cropper = null;
            let activeState = null;
            let activeSourceFile = null;
            let cropSourceUrl = null;

            function replaceInputFile(input, file) {
                const transfer = new DataTransfer();
                transfer.items.add(file);
                input.files = transfer.files;
            }

            function setCropError(message) {
                cropError.textContent = message || '';
                cropError.classList.toggle('is-visible', Boolean(message));
            }

            function closeCropEditor() {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                if (cropSourceUrl) {
                    URL.revokeObjectURL(cropSourceUrl);
                    cropSourceUrl = null;
                }
                modal.hidden = true;
                document.body.style.overflow = '';
                setCropError('');
                activeState = null;
                activeSourceFile = null;
            }

            function cancelCropEditor() {
                if (activeState && activeState.currentFile) {
                    replaceInputFile(activeState.input, activeState.currentFile);
                } else if (activeState) {
                    activeState.input.value = '';
                    activeState.preview.src = activeState.originalPreview;
                }
                closeCropEditor();
            }

            function commitFile(state, file, sourceFile) {
                if (state.previewUrl) {
                    URL.revokeObjectURL(state.previewUrl);
                }
                replaceInputFile(state.input, file);
                state.sourceFile = sourceFile;
                state.currentFile = file;
                state.previewUrl = URL.createObjectURL(file);
                state.preview.src = state.previewUrl;
                state.adjustButton.classList.add('is-visible');
                closeCropEditor();
            }

            function openCropEditor(state, file) {
                if (!window.Cropper) {
                    commitFile(state, file, file);
                    return;
                }

                activeState = state;
                activeSourceFile = file;
                cropSourceUrl = URL.createObjectURL(file);
                cropImage.src = cropSourceUrl;
                modal.hidden = false;
                document.body.style.overflow = 'hidden';
                setCropError('');

                cropper = new Cropper(cropImage, {
                    aspectRatio: NaN,
                    autoCropArea: 0.92,
                    background: false,
                    dragMode: 'move',
                    guides: true,
                    responsive: true,
                    restore: false,
                    viewMode: 1
                });
            }

            document.querySelectorAll('[data-logo-field]').forEach(function (field) {
                const input = field.querySelector('[data-logo-input]');
                const preview = field.querySelector('[data-logo-preview-image]');
                const adjustButton = field.querySelector('[data-logo-adjust-crop]');

                if (!input || !preview || !adjustButton) {
                    return;
                }

                const state = {
                    input: input,
                    preview: preview,
                    adjustButton: adjustButton,
                    originalPreview: preview.src,
                    sourceFile: null,
                    currentFile: null,
                    previewUrl: null
                };

                preview.addEventListener('error', function () {
                    const fallback = preview.dataset.logoFallback;
                    if (fallback && preview.src !== fallback) {
                        preview.src = fallback;
                    }
                });

                input.addEventListener('change', function () {
                    const file = input.files && input.files[0];
                    if (!file) {
                        return;
                    }

                    if (!['image/png', 'image/jpeg', 'image/webp'].includes(file.type)) {
                        input.value = '';
                        window.alert('Choose a PNG, JPG, or WebP image.');
                        return;
                    }

                    openCropEditor(state, file);
                });

                adjustButton.addEventListener('click', function () {
                    if (state.sourceFile) {
                        openCropEditor(state, state.sourceFile);
                    }
                });
            });

            cancelButtons.forEach(function (button) {
                button.addEventListener('click', cancelCropEditor);
            });

            useFullButton.addEventListener('click', function () {
                if (activeState && activeSourceFile) {
                    commitFile(activeState, activeSourceFile, activeSourceFile);
                }
            });

            applyButton.addEventListener('click', function () {
                if (!cropper || !activeState || !activeSourceFile) {
                    return;
                }

                const cropData = cropper.getData(true);
                if (cropData.width < 64 || cropData.height < 64) {
                    setCropError('The selected crop must be at least 64 × 64 pixels.');
                    return;
                }

                const canvas = cropper.getCroppedCanvas({
                    maxHeight: 4096,
                    maxWidth: 4096,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high'
                });
                const state = activeState;
                const sourceFile = activeSourceFile;
                const mimeType = sourceFile.type;
                const extension = mimeType === 'image/jpeg' ? 'jpg' : mimeType.split('/')[1];

                applyButton.disabled = true;
                canvas.toBlob(function (blob) {
                    applyButton.disabled = false;
                    if (!blob) {
                        setCropError('Unable to crop this image. Try using the full image instead.');
                        return;
                    }

                    const croppedFile = new File([blob], 'cropped-logo.' + extension, {
                        type: mimeType,
                        lastModified: Date.now()
                    });
                    commitFile(state, croppedFile, sourceFile);
                }, mimeType, 0.92);
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !modal.hidden) {
                    cancelCropEditor();
                }
            });
        })();
    </script>
@endsection
