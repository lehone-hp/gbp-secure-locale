<?php

namespace App\Http\Controllers\Web\Account;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('web.account.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Staff sign in through /admin; this form is for customers only
        if (! Auth::attempt($credentials + ['role' => User::ROLE_USER], $request->boolean('remember'))) {
            return back()->withErrors(['email' => __('account.invalid_credentials')])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('account.orders'));
    }

    public function showRegister()
    {
        return view('web.account.auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', PasswordRule::min(10)->mixedCase()->numbers()],
        ]);

        $user = User::create([
            'name' => Str::before($data['email'], '@'),
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => User::ROLE_USER,
        ]);
        $user->update(['customer_number' => 'CLI-'.str_pad($user->id, 5, '0', STR_PAD_LEFT)]);

        $this->sendVerification($user);

        Auth::login($user);

        return redirect()->route('verification.notice');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('index');
    }

    public function verificationNotice(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->route('account.orders')
            : view('web.account.auth.verify-email');
    }

    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect()->route('account.orders')->with('success', __('account.email_verified'));
    }

    public function resendVerification(Request $request)
    {
        $this->sendVerification($request->user());

        return back()->with('success', __('account.verification_sent'));
    }

    public function showForgotPassword()
    {
        return view('web.account.auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        try {
            Password::sendResetLink($request->only('email'));
        } catch (\Throwable $e) {
            Log::error('Password reset email failed: '.$e->getMessage());
        }

        // Same answer whether or not the address exists, so accounts cannot be probed
        return back()->with('success', __('account.reset_link_sent'));
    }

    public function showResetPassword(Request $request, string $token)
    {
        return view('web.account.auth.reset-password', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(10)->mixedCase()->numbers()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => $password])->save();

                // Following the reset link proves the address belongs to them
                if (! $user->hasVerifiedEmail() && $user->markEmailAsVerified()) {
                    event(new Verified($user));
                }
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('account.login')->with('success', __('account.password_reset_done'))
            : back()->withErrors(['email' => __('account.reset_link_invalid')])->onlyInput('email');
    }

    private function sendVerification(User $user): void
    {
        try {
            if (! $user->hasVerifiedEmail()) {
                event(new Registered($user));
            }
        } catch (\Throwable $e) {
            Log::error('Verification email failed: '.$e->getMessage());
        }
    }
}
