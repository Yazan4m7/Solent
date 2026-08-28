@extends('layouts.app', ['pageSlug' => 'Edit Tenant Logo', 'platformAdminPage' => true])

@section('content')
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

    <script>
        (function () {
            document.querySelectorAll('[data-logo-field]').forEach(function (field) {
                const input = field.querySelector('[data-logo-input]');
                const image = field.querySelector('[data-logo-preview-image]');
                let previewUrl = null;

                if (!input || !image) {
                    return;
                }

                image.addEventListener('error', function () {
                    const fallback = image.dataset.logoFallback;
                    if (fallback && image.src !== fallback) {
                        image.src = fallback;
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
                });
            });
        })();
    </script>
@endsection
