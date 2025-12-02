<!DOCTYPE html>
<html lang="en">
<head>
    @php($brandLogo = asset($brandingLogoPath ?? config('branding.defaults.logo_path')))
    @php($brandName = $brandingName ?? config('branding.defaults.name'))
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $brandName }} | Sign in</title>
    <link rel="icon" href="{{ asset($brandingFaviconPath ?? config('branding.defaults.favicon_path')) }}">
    @brandStyles
    <style>
        :root {
            --korvion-primary: var(--brand-primary, #c89b3c);
            --korvion-secondary: var(--brand-secondary, #e6c77a);
            --korvion-surface: #111722;
            --korvion-surface-alt: #0b1117;
            --korvion-text: #f5f5f5;
            --korvion-muted: #a6acb8;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            min-height: 100%;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: radial-gradient(120% 120% at 20% 20%, rgba(200, 155, 60, 0.15), transparent 45%), radial-gradient(120% 120% at 80% 0%, rgba(230, 199, 122, 0.12), transparent 40%), var(--korvion-surface);
            color: var(--korvion-text);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 20px;
        }

        .login-layout {
            max-width: 1100px;
            width: 100%;
            display: flex;
            gap: 96px;
            align-items: center;
            justify-content: center;
        }

        .left-section {
            flex: 1;
            color: var(--korvion-text);
            text-shadow: 0 0 12px rgba(0, 0, 0, 0.5);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .left-section .logo {
            display: flex;
            align-items: center;
            font-weight: 700;
            font-size: 36px;
            gap: 14px;
            margin-bottom: 12px;
            letter-spacing: 0.5px;
        }

        .left-section .logo img {
            width: 68px;
            height: 68px;
            border-radius: 14px;
            object-fit: contain;
            background: rgba(255, 255, 255, 0.02);
            padding: 8px;
            box-shadow: 0 0 24px rgba(0, 0, 0, 0.35);
        }

        .left-section p {
            font-size: 17px;
            max-width: 360px;
            line-height: 1.5;
            color: var(--korvion-muted);
        }

        .right-section {
            flex: 0 0 400px;
            background: var(--korvion-surface-alt);
            padding: 44px 42px;
            border-radius: 12px;
            box-shadow: 0 18px 44px rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(200, 155, 60, 0.16);
        }

        .right-section h1 {
            margin: 0 0 12px 0;
            font-size: 28px;
            font-weight: 700;
            color: var(--korvion-text);
        }

        .new-user {
            font-size: 14px;
            margin-bottom: 24px;
            color: var(--korvion-muted);
        }

        .new-user a {
            color: var(--korvion-secondary);
            text-decoration: none;
            font-weight: 600;
        }

        .new-user a:hover {
            text-decoration: underline;
        }

        label {
            display: block;
            font-size: 14px;
            margin-bottom: 6px;
            font-weight: 600;
            color: var(--korvion-text);
        }

        input[type="username"],
        input[type="password"] {
            width: 100%;
            height: 44px;
            padding: 10px 12px;
            font-size: 16px;
            border: 1.3px solid rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            outline-offset: 2px;
            transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out, background 0.2s ease;
            background: rgba(255, 255, 255, 0.03);
            color: var(--korvion-text);
        }

        input[type="username"]:focus,
        input[type="password"]:focus {
            border-color: var(--korvion-primary);
            box-shadow: 0 0 10px rgba(200, 155, 60, 0.35);
        }

        input.is-invalid {
            border-color: #d93025;
        }

        .invalid-feedback {
            display: block;
            font-size: 13px;
            color: #f6b0a0;
            margin-top: 6px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 22px 0 30px 0;
            font-weight: 500;
            font-size: 14px;
            color: var(--korvion-text);
        }

        .remember-me input {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--korvion-primary);
        }

        button.signin-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--korvion-primary), var(--korvion-secondary));
            color: #0b0e14;
            border: none;
            padding: 12px 0;
            font-weight: 700;
            font-size: 16px;
            border-radius: 22px;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
            box-shadow: 0 12px 30px rgba(200, 155, 60, 0.28);
        }

        button.signin-btn:hover {
            transform: translateY(-1px);
            filter: brightness(1.02);
        }

        button.signin-btn:active {
            transform: translateY(0);
        }

        button.signin-btn:disabled {
            background: rgba(255, 255, 255, 0.08);
            color: var(--korvion-muted);
            cursor: not-allowed;
            box-shadow: none;
        }

        .social-signin {
            margin-top: 22px;
        }

        .social-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 12px;
            margin-bottom: 12px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.02);
            font-size: 15px;
            gap: 10px;
            cursor: pointer;
            color: var(--korvion-text);
            transition: border-color 0.2s ease, background 0.2s ease;
        }

        .social-btn:hover {
            border-color: rgba(255, 255, 255, 0.16);
            background: rgba(255, 255, 255, 0.04);
        }

        .social-btn img {
            width: 20px;
            height: 20px;
        }

        .links {
            margin-top: 26px;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .links a {
            color: var(--korvion-secondary);
            text-decoration: none;
            font-weight: 600;
        }

        .links a:hover {
            text-decoration: underline;
        }

        @media (max-width: 800px) {
            .login-layout {
                flex-direction: column;
                gap: 40px;
                max-width: 90vw;
            }

            .left-section {
                align-items: center;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="login-layout" role="main">
        <div class="left-section" aria-label="{{ $brandName }} brand">
            <div class="logo">
                <img src="{{ $brandLogo }}" alt="{{ $brandName }} logo">
                {{ $brandName }}
            </div>
            <p>Sign in to manage your dental lab workflows, approve cases, and keep every smile on schedule.</p>
        </div>

        <section class="right-section" aria-label="Sign in form">
            <h1>Sign in</h1>
            <div class="new-user">
                New user?
                <a href="{{ route('register') }}">Create an account</a>
            </div>

            @if (session('status'))
                <div class="invalid-feedback" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            @if (count($errors) > 0)
                <div class="invalid-feedback" role="alert" style="color: #f6b0a0;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session('nouser'))
                <div class="invalid-feedback" role="alert" style="color: #f6b0a0;">Wrong username or password</div>
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
                class="@error('email') is-invalid @enderror"
                placeholder="korvion.user"
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
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="@error('password') is-invalid @enderror"
                    placeholder="Enter your password"
                    required
                    autocomplete="current-password"
                >
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

            <div class="links">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">Forgot password?</a>
                @endif
                <a href="{{ route('register') }}">Get help signing in</a>
            </div>
        </section>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Signing in...';
        });
    </script>
</body>
</html>
