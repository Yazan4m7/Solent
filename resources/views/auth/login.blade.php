<!DOCTYPE html>
<html lang="en">
<head>
    @php($brandLogo = asset('images/brands/solent/solent_h_white.svg'))
    @php($brandName = $brandingName ?? "Solent")
    @php($loginBgUrl = asset(file_exists(public_path('images/bg1.png')) ? 'images/bg1.png' : 'images/bg1.jpg'))
    @php($context = is_array($domainContext ?? null) ? $domainContext : [])
    @php($countryCode = strtoupper((string) ($context['country_code'] ?? 'NA')))
    @php($countryName = (string) ($context['country_name'] ?? 'Default'))
    @php($countryDatabase = (string) ($context['database'] ?? config('database.connections.mysql.database')))
    @php($countryClass = $countryCode === 'PS' ? 'is-ps' : ($countryCode === 'JO' ? 'is-jo' : 'is-default'))
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $brandName }} | Sign in</title>
    <link rel="icon" href="{{ asset($brandingFaviconPath ?? config('branding.defaults.favicon_path')) }}?v=20260603-favwhite1">
    @brandStyles
    <style>
        :root {
            --brand-primary: {{ config('branding.defaults.primary_color') }};
            --brand-secondary: {{ config('branding.defaults.secondary_color') }};
            --brand-accent: {{ config('branding.defaults.accent_color') }};

            --Korvex-primary: #33899a;
            --Korvex-secondary: #4ba1d7;

            --login-bg: #f8f9fa;
            --login-surface: #ffffff;
            --login-surface-2: #f1f5f9;
            --login-border: #e2e8f0;
            --login-text: #1e293b;
            --login-muted: #64748b;
            --login-shadow: 0 18px 44px rgba(15, 23, 42, 0.10);
            --login-focus: rgba(51, 137, 154, 0.18);
        }

        body.login-page * {
            box-sizing: border-box;
        }

        html,
        body.login-page {
            margin: 0;
            min-height: 100%;
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #449cc8;
            background-image:
                linear-gradient(180deg, rgba(68, 156, 200, 0.28), rgba(68, 156, 200, 0.28)),
                url('{{ $loginBgUrl }}');
            background-repeat: no-repeat;
            background-position: center;
            background-size: unset;
            background-blend-mode: multiply;
            color: var(--login-text);
            display: flex;
            align-items: center;
            justify-content: center;

        }

        body.login-page .auth-split {
            max-width: 1160px;
            width: 100%;
            display: flex;
            flex-direction: row-reverse;
            gap: 20rem;
            align-items: stretch;
            justify-content: center;
        }

        body.login-page .auth-left {
            flex: 1 1 auto;
            display: flex;
            align-items: center;





        }

        body.login-page .auth-brand {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
            color: #ffffff;
            max-width: 520px;
        }

        body.login-page .auth-brand-logo {

            width: 22rem;
            height: auto;
            max-height: 240px;
            object-fit: contain;

        }

        body.login-page .auth-brand-name {
            font-weight: 800;
            letter-spacing: 0.01em;
            font-size: 28px;
            line-height: 1.15;
            text-shadow: 0 1px 10px rgba(15, 23, 42, 0.35);
        }

        body.login-page .auth-brand-tagline {
            font-size: 16px;
            line-height: 1.55;
            color: rgba(255, 255, 255, 0.90);
            text-shadow: 0 1px 10px rgba(15, 23, 42, 0.35);
        }

        body.login-page .auth-right {

            max-width: 520px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 18px;
            background: rgba(255, 255, 255, 0.14);


        }

        body.login-page .right-section {
            flex: 0 0 400px;
            background: rgba(241, 245, 249, 0.82);
            padding: 44px 42px;
            border-radius: 12px;
            box-shadow: 0 18px 44px rgba(15, 23, 42, 0.18);
            border: 1px solid rgba(15, 23, 42, 0.12);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        body.login-page .right-section h1 {
            margin: 0 0 12px 0;
            font-size: 28px;
            font-weight: 700;
            color: var(--login-text);
        }

        body.login-page .country-context {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border: 1px solid rgba(15, 23, 42, 0.12);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.74);
            color: var(--login-text);
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        body.login-page .country-context-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: #64748b;
        }

        body.login-page .country-context.is-jo .country-context-dot {
            background: #007a3d;
        }

        body.login-page .country-context.is-ps .country-context-dot {
            background: #ce1126;
        }

        body.login-page .country-database {
            margin: 0 0 18px 0;
            font-size: 12px;
            color: var(--login-muted);
            word-break: break-word;
        }

        body.login-page .new-user {
            font-size: 14px;
            margin-bottom: 24px;
            color: var(--login-muted);
        }

        body.login-page .new-user a {
            color: var(--Korvex-primary);
            text-decoration: none;
            font-weight: 600;
        }

        body.login-page .new-user a:hover {
            text-decoration: underline;
        }

        body.login-page label {
            display: block;
            font-size: 14px;
            margin-bottom: 6px;
            font-weight: 600;
            color: var(--login-text);
        }

        body.login-page input[type="username"],
        body.login-page input[type="password"] {
            width: 100%;
            height: 44px;
            padding: 10px 12px;
            font-size: 16px;
            border: 1px solid var(--login-border);
            border-radius: 8px;
            outline-offset: 2px;
            transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out, background 0.2s ease;
            background: rgba(255, 255, 255, 0.92);
            color: var(--login-text);
        }

        body.login-page input[type="username"]:focus,
        body.login-page input[type="password"]:focus {
            border-color: var(--Korvex-primary);
            box-shadow: 0 0 0 4px var(--login-focus);
        }

        body.login-page input.is-invalid {
            border-color: #d93025;
        }

        body.login-page .invalid-feedback {
            display: block;
            font-size: 13px;
            color: #f6b0a0;
            margin-top: 6px;
        }

        body.login-page .remember-me {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 22px 0 30px 0;
            font-weight: 500;
            font-size: 14px;
            color: var(--login-text);
        }

        body.login-page .remember-me input {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--Korvex-primary);
        }

        body.login-page button.signin-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--Korvex-primary), var(--Korvex-secondary));
            color: #ffffff;
            border: none;
            padding: 12px 0;
            font-weight: 700;
            font-size: 16px;
            border-radius: 22px;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
            box-shadow: 0 12px 28px rgba(51, 137, 154, 0.22);
        }

        body.login-page button.signin-btn:hover {
            transform: translateY(-1px);
            filter: brightness(1.02);
        }

        body.login-page button.signin-btn:active {
            transform: translateY(0);
        }

        body.login-page button.signin-btn:disabled {
            background: var(--login-surface-2);
            color: var(--login-muted);
            cursor: not-allowed;
            box-shadow: none;
        }

        body.login-page .social-signin {
            margin-top: 22px;
        }

        body.login-page .social-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;

            margin-bottom: 12px;
            border: 1px solid var(--login-border);
            border-radius: 8px;
            background: var(--login-surface);
            font-size: 15px;
            gap: 10px;
            cursor: pointer;
            color: var(--login-text);
            transition: border-color 0.2s ease, background 0.2s ease;
        }

        body.login-page .social-btn:hover {
            border-color: rgba(51, 137, 154, 0.30);
            background: #f8fbfc;
        }

        body.login-page .social-btn img {
            width: 20px;
            height: 20px;
        }

        body.login-page .links {
            margin-top: 26px;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        body.login-page .links a {
            color: var(--Korvex-primary);
            text-decoration: none;
            font-weight: 600;
        }

        body.login-page .links a:hover {
            text-decoration: underline;
        }

        @media (max-width: 991.98px) {
            body.login-page .auth-split {
                flex-direction: column;
                gap: 18px;
                max-width: 92vw;
            }

            body.login-page .auth-right {
                order: 2;
                flex-basis: auto;
                max-width: 100%;
                padding: 18px;
            }

            body.login-page .right-section {
                flex: 1 1 auto;
                width: 100%;
                max-width: 520px;
            }

            body.login-page .auth-left {
                order: 1;

            }

            body.login-page .auth-brand {
                align-items: center;
                text-align: center;
                max-width: 100%;
            }

            body.login-page .auth-brand-logo {
                width: 20rem;
                max-height: 220px;
            }
        }
    </style>
    <link href="{{ asset('assets') }}/css/solent-demo.css?v=20260605-glass3" rel="stylesheet">
    <style>
        /* Keep the true Solent mark on login; do not render as a compact icon badge. */
        body.login-page .auth-brand {
            display: flex !important;
            flex-direction: column !important;
            align-items: flex-start !important;
            justify-content: center !important;
            gap: 24px !important;
            max-width: 360px !important;
        }

        body.login-page .auth-brand-logo {
            width: clamp(260px, 21vw, 340px) !important;
            height: auto !important;
            max-height: none !important;
            padding: 0 !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            filter: none !important;
            object-fit: contain !important;
        }

        body.login-page .auth-brand::after {
            margin-top: 0 !important;
            max-width: 330px !important;
            font-size: 16px !important;
            line-height: 1.55 !important;
        }

        body.login-page .auth-brand-name,
        body.login-page .auth-brand-name::before {
            display: none !important;
            content: none !important;
        }
    </style>
</head>
<body class="login-page">
    <div class="auth-split" role="main">
        <aside class="auth-left" aria-label="{{ $brandName }} branding">
            <div class="auth-brand">
                <img class="auth-brand-logo" src="{{ $brandLogo }}" alt="{{ $brandName }} logo">
                <div class="auth-brand-name"></div>

            </div>
        </aside>

        <main class="auth-right">
            <section class="right-section" aria-label="Sign in form">
                <h1>Sign in</h1>
                <div class="country-context {{ $countryClass }}" aria-label="Country and branch indicator">
                    <span class="country-context-dot" aria-hidden="true"></span>
                    <span>{{ $countryName }} ({{ $countryCode }})</span>
                </div>
                <p class="country-database">Database: {{ $countryDatabase }}</p>

            @if (session('status'))
                <div class="invalid-feedback" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            @if(session('unauthorized'))
                <div class="invalid-feedback" role="alert" style="color: #f6b0a0;">Unauthorized to access admin panel</div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
                @csrf
            <label for="username">Username</label>
            <input
                type="username"
                id="username"
                name="username"
                value="{{ old('username') }}"
                class="@error('username') is-invalid @enderror"
                placeholder="Enter your username"
                required
                autofocus
                autocomplete="username"
            >
            @error('username')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

                <label for="password" style="margin-top: 18px;">Password</label>
                <div class="password-field">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="password-input @error('password') is-invalid @enderror"
                        placeholder="Enter your password"
                        required
                        autocomplete="current-password"
                    >
                    <button
                        type="button"
                        class="password-toggle"
                        id="passwordToggle"
                        aria-label="Show password"
                        aria-controls="password"
                        aria-pressed="false"
                    >
                        <svg class="password-toggle-eye" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M12 5.5c5.1 0 8.6 4.2 9.7 5.8.3.4.3.9 0 1.3-1.1 1.6-4.6 5.8-9.7 5.8s-8.6-4.2-9.7-5.8c-.3-.4-.3-.9 0-1.3 1.1-1.6 4.6-5.8 9.7-5.8Zm0 2c-4 0-6.9 3.2-7.8 4.5.9 1.3 3.8 4.5 7.8 4.5s6.9-3.2 7.8-4.5C18.9 10.7 16 7.5 12 7.5Zm0 1.7a2.8 2.8 0 1 1 0 5.6 2.8 2.8 0 0 1 0-5.6Z" fill="currentColor"/>
                        </svg>
                        <svg class="password-toggle-eye-off" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M3.3 2 22 20.7 20.7 22l-3.2-3.2a10.8 10.8 0 0 1-5.5 1.7c-5.1 0-8.6-4.2-9.7-5.8-.3-.4-.3-.9 0-1.3.6-.9 2-2.7 4-4.1L2 3.3 3.3 2Zm4.4 8.7A14.1 14.1 0 0 0 4.2 14c.9 1.3 3.8 4.5 7.8 4.5 1.5 0 2.8-.4 4-1.1l-2.1-2.1A2.8 2.8 0 0 1 10.7 12l-3-1.3Zm4.3-6.2c5.1 0 8.6 4.2 9.7 5.8.3.4.3.9 0 1.3-.4.7-1.4 1.9-2.7 3.1l-1.4-1.4c.9-.8 1.6-1.6 2.1-2.3-.9-1.3-3.8-4.5-7.8-4.5-1 0-1.9.2-2.8.6L7.6 5.7c1.3-.7 2.8-1.2 4.4-1.2Z" fill="currentColor"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">Remember me</label>
                </div>

                <button type="submit" class="signin-btn" id="submitBtn">Continue</button>
            </form>

{{--            <div class="social-signin">--}}
{{--                <button class="social-btn" type="button">--}}
{{--                    <img src="{{ asset('assets/images/google-logo.png') }}" alt="Google logo">--}}
{{--                    Continue with Google--}}
{{--                </button>--}}
{{--                <button class="social-btn" type="button">--}}
{{--                    <img src="{{ asset('assets/images/apple-logo.png') }}" alt="Apple logo">--}}
{{--                    Continue with Apple--}}
{{--                </button>--}}
{{--            </div>--}}

            </section>
        </main>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.getElementById('passwordToggle');

        if (passwordInput && passwordToggle) {
            passwordToggle.addEventListener('click', function() {
                const shouldShow = passwordInput.type === 'password';
                passwordInput.type = shouldShow ? 'text' : 'password';
                passwordToggle.classList.toggle('is-visible', shouldShow);
                passwordToggle.setAttribute('aria-pressed', shouldShow ? 'true' : 'false');
                passwordToggle.setAttribute('aria-label', shouldShow ? 'Hide password' : 'Show password');
            });
        }

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Signing in...';
        });
    </script>
</body>
</html>
