<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ trans('ui.direction') }}">
<head>
    @php($ui = trans('ui.dom'))
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $ui['Platform Administration'] ?? 'Platform Administration' }} | {{ $ui['Sign in'] ?? 'Sign in' }}</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='16' fill='%230d172a'/%3E%3Cpath d='M43 19H27c-5 0-9 3-9 8s4 7 9 8l8 2c2 .5 3 1 3 3s-2 3-5 3H20v6h14c7 0 11-4 11-10 0-5-3-7-9-9l-8-2c-2-.5-3-1-3-2 0-2 2-2 4-2h14z' fill='%2366e3ff'/%3E%3C/svg%3E">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/site-typography.css') }}" rel="stylesheet">
    <style>
        :root {
            color-scheme: dark;
            --admin-bg: #07101f;
            --admin-panel: rgba(10, 22, 40, 0.88);
            --admin-panel-strong: #0d1b30;
            --admin-border: rgba(148, 193, 225, 0.18);
            --admin-text: #f8fbff;
            --admin-muted: #9eb0c6;
            --admin-accent: #66e3ff;
            --admin-accent-strong: #37c7ed;
            --admin-danger: #fda4af;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html,
        body {
            min-height: 100%;
        }

        body.platform-login {
            align-items: center;
            background:
                radial-gradient(circle at 14% 12%, rgba(55, 199, 237, 0.17), transparent 30%),
                linear-gradient(115deg, rgba(4, 11, 23, 0.98), rgba(9, 21, 39, 0.91)),
                url('{{ asset('images/auth/login-bg.png') }}') center / cover no-repeat;
            color: var(--admin-text);
            display: flex;
            font-family: 'Cairo', system-ui, sans-serif;
            justify-content: center;
            margin: 0;
            min-height: 100vh;
            padding: 28px;
        }

        .platform-login__language {
            position: fixed;
            right: 20px;
            top: 18px;
            z-index: 3;
        }

        html[dir="rtl"] .platform-login__language {
            left: 20px;
            right: auto;
        }

        .platform-login__shell {
            background: var(--admin-panel);
            border: 1px solid var(--admin-border);
            border-radius: 26px;
            box-shadow: 0 32px 90px rgba(0, 0, 0, 0.44);
            display: grid;
            grid-template-columns: minmax(300px, 0.92fr) minmax(390px, 1.08fr);
            max-width: 980px;
            min-height: 570px;
            overflow: hidden;
            width: 100%;
        }

        .platform-login__identity {
            background:
                linear-gradient(155deg, rgba(102, 227, 255, 0.12), transparent 42%),
                #091528;
            border-inline-end: 1px solid var(--admin-border);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 54px 46px 44px;
        }

        html[dir="rtl"] .platform-login__identity {
            border-inline-end: 0;
            border-inline-start: 1px solid var(--admin-border);
        }

        .platform-login__wordmark {
            align-items: center;
            display: flex;
            gap: 15px;
        }

        .platform-login__mark {
            align-items: center;
            background: linear-gradient(145deg, var(--admin-accent), #3a8dff);
            border-radius: 14px;
            box-shadow: 0 12px 30px rgba(55, 199, 237, 0.23);
            color: #06111f;
            display: flex;
            flex: 0 0 54px;
            font-size: 30px;
            font-weight: 800;
            height: 54px;
            justify-content: center;
            line-height: 1;
        }

        .platform-login__brand-name {
            display: block;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.4px;
            line-height: 1;
        }

        .platform-login__brand-label {
            color: var(--admin-accent);
            display: block;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.8px;
            margin-top: 7px;
            text-transform: uppercase;
        }

        .platform-login__intro {
            margin-top: 72px;
        }

        .platform-login__intro h1 {
            font-size: clamp(30px, 4vw, 43px);
            letter-spacing: -1px;
            line-height: 1.13;
            margin: 0 0 16px;
        }

        .platform-login__intro p,
        .platform-login__security {
            color: var(--admin-muted);
            font-size: 14px;
            line-height: 1.7;
            margin: 0;
        }

        .platform-login__security {
            align-items: center;
            display: flex;
            gap: 9px;
        }

        .platform-login__security svg {
            color: var(--admin-accent);
            flex: 0 0 17px;
            height: 17px;
            width: 17px;
        }

        .platform-login__form-panel {
            align-self: center;
            padding: 54px 58px;
        }

        .platform-login__eyebrow {
            color: var(--admin-accent);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.6px;
            margin: 0 0 9px;
            text-transform: uppercase;
        }

        .platform-login__form-panel h2 {
            font-size: 29px;
            line-height: 1.2;
            margin: 0 0 8px;
        }

        .platform-login__subtitle {
            color: var(--admin-muted);
            font-size: 14px;
            margin: 0 0 30px;
        }

        .platform-login__alert {
            background: rgba(244, 63, 94, 0.11);
            border: 1px solid rgba(251, 113, 133, 0.27);
            border-radius: 10px;
            color: var(--admin-danger);
            font-size: 13px;
            margin-bottom: 18px;
            padding: 10px 12px;
        }

        .platform-login__field {
            margin-bottom: 18px;
        }

        .platform-login__field label {
            color: #cbd7e5;
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 7px;
        }

        .platform-login__input {
            position: relative;
        }

        .platform-login__input > svg {
            color: #71839a;
            height: 18px;
            inset-inline-start: 15px;
            pointer-events: none;
            position: absolute;
            top: 15px;
            width: 18px;
        }

        .platform-login__input input {
            background: var(--admin-panel-strong);
            border: 1px solid var(--admin-border);
            border-radius: 11px;
            color: var(--admin-text);
            font: inherit;
            font-size: 14px;
            height: 50px;
            outline: none;
            padding-inline: 46px 14px;
            transition: border-color .16s ease, box-shadow .16s ease;
            width: 100%;
        }

        .platform-login__input input:focus {
            border-color: rgba(102, 227, 255, 0.65);
            box-shadow: 0 0 0 3px rgba(102, 227, 255, 0.09);
        }

        .platform-login__input input.is-invalid {
            border-color: rgba(251, 113, 133, 0.72);
        }

        .platform-login__error {
            color: var(--admin-danger);
            display: block;
            font-size: 12px;
            margin-top: 6px;
        }

        .platform-login__options {
            align-items: center;
            color: var(--admin-muted);
            display: flex;
            font-size: 13px;
            justify-content: space-between;
            margin: 4px 0 25px;
        }

        .platform-login__remember {
            align-items: center;
            cursor: pointer;
            display: flex;
            gap: 8px;
        }

        .platform-login__remember input {
            accent-color: var(--admin-accent-strong);
            height: 16px;
            margin: 0;
            width: 16px;
        }

        .platform-login__forgot {
            color: var(--admin-accent);
            text-decoration: none;
        }

        .platform-login__forgot:hover,
        .platform-login__forgot:focus-visible {
            text-decoration: underline;
        }

        .platform-login__submit {
            background: linear-gradient(135deg, var(--admin-accent), var(--admin-accent-strong));
            border: 0;
            border-radius: 11px;
            color: #06111f;
            cursor: pointer;
            font: inherit;
            font-size: 15px;
            font-weight: 800;
            height: 50px;
            transition: filter .16s ease, transform .1s ease;
            width: 100%;
        }

        .platform-login__submit:hover,
        .platform-login__submit:focus-visible {
            filter: brightness(1.06);
        }

        .platform-login__submit:active {
            transform: translateY(1px);
        }

        .platform-login__submit:disabled {
            cursor: default;
            opacity: .7;
        }

        .platform-login__notice {
            color: #71839a;
            font-size: 12px;
            line-height: 1.55;
            margin: 20px 0 0;
            text-align: center;
        }

        @media (max-width: 820px) {
            body.platform-login {
                align-items: flex-start;
                padding: 58px 20px 24px;
            }

            .platform-login__shell {
                grid-template-columns: 1fr;
                max-width: 540px;
            }

            .platform-login__identity {
                border-block-end: 1px solid var(--admin-border);
                border-inline: 0;
                gap: 34px;
                padding: 32px;
            }

            html[dir="rtl"] .platform-login__identity {
                border-inline: 0;
            }

            .platform-login__intro {
                margin-top: 28px;
            }

            .platform-login__security {
                display: none;
            }

            .platform-login__form-panel {
                padding: 38px 32px 42px;
            }
        }

        @media (max-width: 430px) {
            body.platform-login {
                padding: 54px 12px 16px;
            }

            .platform-login__shell {
                border-radius: 19px;
            }

            .platform-login__identity,
            .platform-login__form-panel {
                padding-inline: 22px;
            }

            .platform-login__identity {
                padding-block: 26px;
            }

            .platform-login__intro h1 {
                font-size: 29px;
            }

            .platform-login__form-panel {
                padding-block: 32px 36px;
            }

            .platform-login__options {
                align-items: flex-start;
                flex-direction: column;
                gap: 13px;
            }
        }
    </style>
</head>
<body class="platform-login" data-portal="platform-admin">
    <x-language-switcher class="platform-login__language" />

    <main class="platform-login__shell">
        <section class="platform-login__identity" aria-label="{{ $ui['Solent Platform Administration'] ?? 'Solent Platform Administration' }}">
            <div>
                <div class="platform-login__wordmark">
                    <span class="platform-login__mark" aria-hidden="true">S</span>
                    <span>
                        <strong class="platform-login__brand-name">Solent</strong>
                        <span class="platform-login__brand-label">{{ $ui['Platform Administration'] ?? 'Platform Administration' }}</span>
                    </span>
                </div>

                <div class="platform-login__intro">
                    <h1>{{ $ui['Tenant Control Center'] ?? 'Tenant Control Center' }}</h1>
                    <p>{{ $ui['Manage tenant provisioning, domains, and account isolation from one secure workspace.'] ?? 'Manage tenant provisioning, domains, and account isolation from one secure workspace.' }}</p>
                </div>
            </div>

            <div class="platform-login__security">
                <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path d="M10 2.5L16 5.2v4.6c0 3.5-2.5 6.4-6 7.7-3.5-1.3-6-4.2-6-7.7V5.2L10 2.5Z" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M7.4 10l1.7 1.7 3.7-3.8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>{{ $ui['Isolated from all tenant workspaces.'] ?? 'Isolated from all tenant workspaces.' }}</span>
            </div>
        </section>

        <section class="platform-login__form-panel" aria-label="{{ $ui['Administrator sign in'] ?? 'Administrator sign in' }}">
            <p class="platform-login__eyebrow">{{ $ui['Authorized administrators only'] ?? 'Authorized administrators only' }}</p>
            <h2>{{ $ui['Welcome back'] ?? 'Welcome back' }}</h2>
            <p class="platform-login__subtitle">{{ $ui['Sign in to manage Solent tenants.'] ?? 'Sign in to manage Solent tenants.' }}</p>

            @if (session('status'))
                <div class="platform-login__alert" role="alert">{{ session('status') }}</div>
            @endif

            @if (session('unauthorized'))
                <div class="platform-login__alert" role="alert">{{ $ui['Unauthorized to access admin panel'] ?? 'Unauthorized to access admin panel' }}</div>
            @endif

            <form id="platformLoginForm" method="POST" action="{{ route('login') }}" autocomplete="on" novalidate>
                @csrf

                <div class="platform-login__field">
                    <label for="username">{{ $ui['Username'] ?? 'Username' }}</label>
                    <div class="platform-login__input">
                        <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <circle cx="10" cy="6.5" r="3" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M4.5 16v-1.5A3.5 3.5 0 018 11h4a3.5 3.5 0 013.5 3.5V16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" class="@error('username') is-invalid @enderror" placeholder="{{ $ui['Enter your username'] ?? 'Enter your username' }}" required autofocus autocomplete="username">
                    </div>
                    @error('username')
                        <span class="platform-login__error" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="platform-login__field">
                    <label for="password">{{ $ui['Password'] ?? 'Password' }}</label>
                    <div class="platform-login__input">
                        <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <rect x="4.5" y="8.5" width="11" height="8" rx="2" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M7 8.5V6a3 3 0 016 0v2.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                        <input type="password" id="password" name="password" class="@error('password') is-invalid @enderror" placeholder="{{ $ui['Enter your password'] ?? 'Enter your password' }}" required autocomplete="current-password">
                    </div>
                    @error('password')
                        <span class="platform-login__error" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="platform-login__options">
                    <label class="platform-login__remember">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>{{ $ui['Remember me'] ?? 'Remember me' }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="platform-login__forgot" href="{{ route('password.request') }}">{{ $ui['Forgot password?'] ?? 'Forgot password?' }}</a>
                    @endif
                </div>

                <button class="platform-login__submit" id="platformLoginSubmit" type="submit">{{ $ui['Sign in'] ?? 'Sign in' }}</button>
            </form>

            <p class="platform-login__notice">{{ $ui['This portal is separate from tenant and laboratory accounts.'] ?? 'This portal is separate from tenant and laboratory accounts.' }}</p>
        </section>
    </main>

    <script>
        (function () {
            const form = document.getElementById('platformLoginForm');
            const submit = document.getElementById('platformLoginSubmit');

            if (form && submit) {
                form.addEventListener('submit', function () {
                    submit.disabled = true;
                    submit.textContent = @json($ui['Signing in...'] ?? 'Signing in...');
                });
            }
        })();
    </script>
</body>
</html>
