<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Korvion | Sign in</title>
    <meta name="description" content="Login page">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;600;700&display=swap">
    <link rel="shortcut icon" href="{{ asset('assets/images/korvion-logo.png') }}" />
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            height: 100%;
        }

        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
            height: 100%;
            background: #000;
            display: grid;
            place-items: center;
            padding: 20px;
        }

        /* Animated Background */
        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #16213e 100%);
            overflow: hidden;
        }

        .background::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: 
                radial-gradient(circle at 20% 50%, rgba(236, 72, 153, 0.1) 0%, transparent 40%),
                radial-gradient(circle at 80% 80%, rgba(59, 130, 246, 0.1) 0%, transparent 40%),
                radial-gradient(circle at 40% 20%, rgba(139, 92, 246, 0.08) 0%, transparent 40%);
            animation: float 25s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(40px, -40px) rotate(120deg); }
            66% { transform: translate(-30px, 30px) rotate(240deg); }
        }

        /* Floating Login Card */
        .login-card {
            background: #fff;
            border-radius: 8px;
            padding: 40px 44px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.4);
            animation: fadeIn 0.6s ease-out;
            position: relative;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }
        
        .login-logo {
            max-width: 80px;
            margin-bottom: 16px;
        }

        .login-title {
            font-size: 24px;
            font-weight: 600;
            color: #1f1f1f;
            margin-bottom: 0;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 10px 12px;
            font-size: 15px;
            border: 1px solid #8a8886;
            border-radius: 4px;
            transition: all 0.2s;
            font-family: inherit;
        }

        .form-input:focus {
            outline: none;
            border-color: #0078d4;
            box-shadow: 0 0 0 2px rgba(0, 120, 212, 0.3);
        }

        /* Remember Me Checkbox */
        .remember-container {
            margin-bottom: 24px;
            display: flex;
            align-items: center;
        }

        .remember-checkbox {
            width: 18px;
            height: 18px;
            margin-right: 8px;
            cursor: pointer;
            accent-color: #0078d4;
        }

        .remember-label {
            font-size: 14px;
            color: #333;
            cursor: pointer;
        }

        /* Submit Button */
        .submit-btn {
            width: 100%;
            padding: 10px 20px;
            background: #0078d4;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .submit-btn:hover {
            background: #106ebe;
        }

        .submit-btn:disabled {
            background: #f3f2f1;
            color: #a19f9d;
            cursor: not-allowed;
        }

        /* Alert Messages */
        .alert {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
            border: 1px solid;
        }

        .alert-danger {
            background: #fdf6f6;
            color: #9b2c2c;
            border-color: #f5c6cb;
        }

        .alert ul {
            margin: 0;
            padding-left: 18px;
        }
        
        /* Spinner */
        .spinner {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.6s linear infinite;
            margin-right: 8px;
            vertical-align: middle;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>

<body>
    <!-- Animated Background -->
    <div class="background"></div>

    <!-- Floating Login Card -->
    <div class="login-card">
        <div class="login-header">
            <img src="{{ asset('assets/images/korvion-logo.png') }}" alt="Korvion" class="login-logo" onerror="this.style.display='none'">
            <h1 class="login-title">Sign in to Korvion</h1>
        </div>

        <!-- Error Messages -->
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(session('nouser'))
            <div class="alert alert-danger">Wrong username or password</div>
        @endif
        @if(session('unauthorized'))
            <div class="alert alert-danger">Unauthorized to access admin panel</div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf
            
            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    class="form-input" 
                    value="{{ old('username') }}"
                    required 
                    autocomplete="username"
                    autofocus
                >
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-input" 
                    required 
                    autocomplete="current-password"
                >
            </div>

            <div class="remember-container">
                <input type="checkbox" id="remember" name="remember" class="remember-checkbox">
                <label for="remember" class="remember-label">Keep me signed in</label>
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">
                Sign in
            </button>
        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner"></span>Signing in...';
        });
    </script>
</body>
</html>
