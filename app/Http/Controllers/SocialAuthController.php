<?php

namespace App\Http\Controllers;

use Hash;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;



class SocialAuthController extends Controller
{
    
// 🟢 Show pages
    public function showLogin() { return view('welcome'); }
    public function showRegister() { return view('register'); }

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

        // Check if a user already exists by email or GitHub ID
        $user = User::where('email', $githubUser->getEmail())
                    ->orWhere('github_id', $githubUser->getId())
                    ->first();

        if ($user) {
            // Update GitHub ID and avatar if not already set
            if (!$user->github_id) {
                $user->github_id = $githubUser->getId();
            }
            $user->avatar = $githubUser->getAvatar();
            $user->save();
        } else {
            // Create a new user
            $user = User::create([
                'name' => $githubUser->getName() ?? $githubUser->getNickname(),
                'email' => $githubUser->getEmail(),
                'github_id' => $githubUser->getId(),
                'password' => Hash::make(Str::random(16)), // random password
                'avatar' => $githubUser->getAvatar(),
            ]);
        }

        // Log in the user
        Auth::login($user);

        return redirect('/dashboard');
    } catch (\Exception $e) {
        return redirect('/login')->with('error', 'GitHub login failed.');
    }
}

    

    // Google

     public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

   public function handleGoogleCallback()
{
    try {
        $googleUser = Socialite::driver('google')->user();

        // Check if a user exists by email or Google ID
        $user = User::where('email', $googleUser->getEmail())
                    ->orWhere('google_id', $googleUser->getId())
                    ->first();

        if ($user) {
            // Update Google ID and avatar if not set
            if (!$user->google_id) {
                $user->google_id = $googleUser->getId();
            }
            $user->avatar = $googleUser->getAvatar();
            $user->save();
        } else {
            // Create a new user
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'password' => bcrypt(Str::random(16)), // random password
                'avatar' => $googleUser->getAvatar(),
            ]);
        }

        // Log in the user
        Auth::login($user);

        return redirect('/dashboard');
    } catch (\Exception $e) {
        return redirect('/login')->with('error', 'Google login failed.');
    }
}

}
