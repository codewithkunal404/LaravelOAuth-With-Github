# 🚀 Laravel 11 Project: “Login + Signup + GitHub OAuth”

### 🧱 Features
- Sign up with name, email, password
- Login with email/password
- Login with GitHub (OAuth)
- Dashboard for logged-in users
- Logout option



### 🛠 Step 1: Create New Project
```
composer create-project laravel/laravel laravel-auth-social
cd laravel-auth-social
```


### 🛠 Step 2: Install Socialite
```
composer require laravel/socialite
```


### 🛠 Step 3: Configure Database
- Update .env:
```

DB_DATABASE=laravel_auth_social
DB_USERNAME=root
DB_PASSWORD=

Run:
php artisan migrate

```

### 🛠 Step 4: GitHub OAuth Setup
- Go to 👉 https://github.com/settings/developers
- Create new OAuth app:
- Application name: Laravel Auth Social
- Homepage URL: http://127.0.0.1:8000
- Authorization callback URL: http://127.0.0.1:8000/auth/github/callback


- Copy Client ID and Client Secret
- Add to .env:
```
GITHUB_CLIENT_ID=your_client_id_here
GITHUB_CLIENT_SECRET=your_client_secret_here
GITHUB_REDIRECT_URI=http://127.0.0.1:8000/auth/github/callback
```

### 🛠 Step 5: Add to config/services.php
```
'github' => [
    'client_id' => env('GITHUB_CLIENT_ID'),
    'client_secret' => env('GITHUB_CLIENT_SECRET'),
    'redirect' => env('GITHUB_REDIRECT_URI'),
],
```

### 🛠 Step 6: Create Auth Controller
```
php artisan make:controller AuthController

```

- app/Http/Controllers/AuthController.php
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // 🟢 Show pages
    public function showLogin() { return view('auth.login'); }
    public function showRegister() { return view('auth.register'); }

    // 🟢 Handle register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect('/dashboard');
    }

    // 🟢 Handle login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            return redirect('/dashboard');
        }

        return back()->with('error', 'Invalid credentials.');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

    // 🟣 GitHub OAuth
    public function redirectToGitHub()
    {
        return Socialite::driver('github')->redirect();
    }

    public function handleGitHubCallback()
    {
        try {
            $githubUser = Socialite::driver('github')->user();

            $user = User::updateOrCreate(
                ['email' => $githubUser->getEmail()],
                [
                    'name' => $githubUser->getName() ?? $githubUser->getNickname(),
                    'github_id' => $githubUser->getId(),
                    'password' => Hash::make(Str::random(16)),
                    'avatar' => $githubUser->getAvatar(),
                ]
            );

            Auth::login($user);

            return redirect('/dashboard');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'GitHub login failed.');
        }
    }
}
```


### 🛠 Step 7: Update User Model
- app/Models/User.php
```
protected $fillable = [
    'name', 'email', 'password', 'github_id', 'avatar',
];

```

- Run migration to add fields:
```
php artisan make:migration add_github_fields_to_users_table --table=users
```

- In the migration file:
```php
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('github_id')->nullable();
        $table->string('avatar')->nullable();
    });
}

```

- Then run:
```
php artisan migrate
```

### 🛠 Step 8: Add Routes
- routes/web.php
```php
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', fn() => redirect('/login'));

// 🔹 Basic Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// 🔹 GitHub OAuth
Route::get('/auth/github', [AuthController::class, 'redirectToGitHub'])->name('github.login');
Route::get('/auth/github/callback', [AuthController::class, 'handleGitHubCallback']);

// 🔹 Protected Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth');
```

### 🛠 Step 9: Create Views
- 📄 resources/views/auth/login.blade.php
```html
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body style="text-align:center;margin-top:80px;font-family:sans-serif;">
    <h2>Login</h2>
    @if (session('error'))
        <p style="color:red;">{{ session('error') }}</p>
    @endif
    <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <input type="email" name="email" placeholder="Email" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <button type="submit">Login</button>
    </form>
    <br>
    <a href="{{ route('github.login') }}">
        <button style="background:black;color:white;padding:8px 16px;">Login with GitHub</button>
    </a>
    <br><br>
    <a href="{{ route('register') }}">Don't have an account? Register</a>
</body>
</html>
```


- 📄 resources/views/auth/register.blade.php

```html
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body style="text-align:center;margin-top:80px;font-family:sans-serif;">
    <h2>Register</h2>
    <form method="POST" action="{{ route('register.post') }}">
        @csrf
        <input type="text" name="name" placeholder="Full Name" required><br><br>
        <input type="email" name="email" placeholder="Email" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <input type="password" name="password_confirmation" placeholder="Confirm Password" required><br><br>
        <button type="submit">Sign Up</button>
    </form>
    <br>
    <a href="{{ route('github.login') }}">
        <button style="background:black;color:white;padding:8px 16px;">Sign up with GitHub</button>
    </a>
    <br><br>
    <a href="{{ route('login') }}">Already have an account? Login</a>
</body>
</html>

```


- 📄 resources/views/dashboard.blade.php
```html
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body style="text-align:center;margin-top:80px;font-family:sans-serif;">
    <h2>Welcome, {{ auth()->user()->name }}</h2>
    @if(auth()->user()->avatar)
        <img src="{{ auth()->user()->avatar }}" width="120" style="border-radius:50%;margin:20px;">
    @endif
    <p>Email: {{ auth()->user()->email }}</p>
    <a href="{{ route('logout') }}">
        <button style="background:red;color:white;padding:8px 16px;">Logout</button>
    </a>
</body>
</html>
```
