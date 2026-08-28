@extends('layouts.app', ['pageSlug' => 'Edit Tenant Logo', 'platformAdminPage' => true])

@section('content')
    <style>
        .tenant-logo-page {
            max-width: 820px;
            margin: 0 auto;
        }

        .tenant-logo-card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
        }

        .tenant-logo-card__body {
            display: grid;
            gap: 28px;
            grid-template-columns: minmax(180px, 240px) minmax(0, 1fr);
            padding: 28px;
        }

        .tenant-logo-preview {
            align-items: center;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 14px;
            display: flex;
            justify-content: center;
            min-height: 210px;
            padding: 22px;
        }

        .tenant-logo-preview img {
            display: block;
            max-height: 164px;
            max-width: 100%;
            object-fit: contain;
        }

        .tenant-logo-placeholder {
            color: #334155;
            font-size: clamp(20px, 3vw, 30px);
            font-weight: 700;
            line-height: 1.2;
            text-align: center;
            word-break: break-word;
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

        .tenant-logo-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 24px;
        }

        @media (max-width: 680px) {
            .tenant-logo-card__body {
                grid-template-columns: 1fr;
                padding: 20px;
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
        }
    </style>

    <div class="container-fluid py-3 tenant-logo-page">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h1 class="h3 mb-1">Edit tenant logo</h1>
                <div class="text-muted">{{ $tenant->name }} · {{ optional($tenant->primaryDomain)->host }}</div>
            </div>
            <a href="{{ route('system.tenants.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>

        <div class="card tenant-logo-card">
            <form method="POST" action="{{ route('system.tenants.logo.update', $tenant) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="tenant-logo-card__body">
                    <div class="tenant-logo-preview" data-logo-preview>
                        @if($currentLogoPath)
                            <img src="{{ asset($currentLogoPath) }}" alt="Current {{ $tenant->name }} logo" data-logo-preview-image>
                            <div class="tenant-logo-placeholder" data-logo-placeholder hidden>{{ $tenant->name }}</div>
                        @else
                            <div class="tenant-logo-placeholder" data-logo-placeholder>{{ $tenant->name }}</div>
                            <img src="" alt="New logo preview" data-logo-preview-image hidden>
                        @endif
                    </div>

                    <div>
                        <label for="tenant-logo" class="font-weight-bold">Choose a new logo</label>
                        <label class="tenant-logo-upload" for="tenant-logo">
                            <input id="tenant-logo" name="logo" type="file" accept="image/png,image/jpeg,image/webp" required data-logo-input>
                        </label>
                        @error('logo')
                            <div class="text-danger mt-2" role="alert">{{ $message }}</div>
                        @enderror
                        <div class="tenant-logo-help">
                            PNG, JPG, or WebP. Maximum 5 MB. Images must be between 64×64 and 4096×4096 pixels.
                            The uploaded image becomes the tenant header, sidebar, and printed-document logo.
                        </div>

                        <div class="tenant-logo-actions">
                            <a href="{{ route('system.tenants.show', $tenant) }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save logo</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const input = document.querySelector('[data-logo-input]');
            const image = document.querySelector('[data-logo-preview-image]');
            const placeholder = document.querySelector('[data-logo-placeholder]');
            let previewUrl = null;

            if (!input || !image) {
                return;
            }

            image.addEventListener('error', function () {
                image.hidden = true;
                if (placeholder) {
                    placeholder.hidden = false;
                }
            });

            input.addEventListener('change', function () {
                const file = input.files && input.files[0];
                if (!file) {
                    return;
                }

                if (previewUrl) {
                    URL.revokeObjectURL(previewUrl);
                }
                previewUrl = URL.createObjectURL(file);
                image.src = previewUrl;
                image.hidden = false;
                if (placeholder) {
                    placeholder.hidden = true;
                }
            });
        })();
    </script>
@endsection
