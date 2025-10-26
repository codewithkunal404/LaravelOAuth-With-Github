<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        /* Body styling */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            margin: 0;
        }

        /* Card container */
        .register-card {
            background: #fff;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 360px;
            text-align: center;
        }

        /* Title */
        .register-card h2 {
            margin-bottom: 25px;
            color: #333;
        }

        /* Input fields */
        .register-card input[type="text"],
        .register-card input[type="email"],
        .register-card input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin: 8px 0 20px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        /* Primary button */
        .register-card button {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            font-size: 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .signup-btn {
            background: #4CAF50;
            color: white;
        }

        .signup-btn:hover {
            background: #45a049;
        }

        /* Social buttons */
        .social-btn {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .social-btn a {
            flex: 1;
            margin: 0 5px;
            text-decoration: none;
        }

        .github-btn {
            background: #24292E;
            color: white;
        }

        .google-btn {
            background: #DB4437;
            color: white;
        }

        .social-btn button:hover {
            opacity: 0.9;
        }

        /* Links */
        .register-card a {
            text-decoration: none;
            color: #555;
            font-size: 14px;
        }

        .register-card a:hover {
            text-decoration: underline;
        }

        /* Error messages */
        .error-msg {
            color: red;
            margin-bottom: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="register-card">
        <h2>Register</h2>

        @if (session('error'))
            <div class="error-msg">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('register.post') }}">
            @csrf
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
            <button type="submit" class="signup-btn">Sign Up</button>
        </form>

        <p>— OR —</p>

        <div class="social-btn">
            <a href="{{ route('github.login') }}">
                <button class="github-btn">GitHub</button>
            </a>
            <a href="{{ route('google.login') }}">
                <button class="google-btn">Google</button>
            </a>
        </div>

        <a href="{{ route('login') }}">Already have an account? Login</a>
    </div>
</body>
</html>
