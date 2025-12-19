<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);


        $user = User::create([
            'username'   => $request->username,
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'name'       => $request->first_name . ' ' . $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
        ]);


        event(new Registered($user));

        Auth::login($user);

        return redirect(route('projects.index'));
    }

    public function checkUsername(Request $request)
    {
        $username = trim((string) $request->query('username', ''));

        // Basic quick validation (match your register rules)
        if ($username === '' || strlen($username) < 3 || strlen($username) > 50) {
            return response()->json([
                'available' => false,
                'reason' => 'invalid',
            ]);
        }

        // allow only letters, numbers, underscores, dashes (alpha_dash)
        if (!preg_match('/^[A-Za-z0-9_-]+$/', $username)) {
            return response()->json([
                'available' => false,
                'reason' => 'invalid',
            ]);
        }

        $exists = User::query()
            ->whereRaw('LOWER(username) = ?', [strtolower($username)])
            ->exists();

        return response()->json([
            'available' => !$exists,
            'reason' => $exists ? 'taken' : 'ok',
        ]);
    }

}
