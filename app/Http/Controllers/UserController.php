<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function create() {
        return view('users.register');
    }

    public function store(Request $request) {
        $formFields = $request->validate([
            'name' => 'required|min:3|max:255|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ]);

        $formFields['password'] = bcrypt($formFields['password']);

        $user = User::create($formFields);

        auth()->login($user);

        return redirect('/')->with('message', 'user created and logged in!');
    }

    public function logout(Request $request) {
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('message', 'You have been logged out!');
    }

    public function login() {
        return view('users.login');
    }

    public function authenticate(Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if(auth()->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()
                ->intended('/')
                ->with('message', 'You have been logged in!');
        }

        return back()
            ->withErrors(['email' => 'These credentials do not match our records.'])
            ->onlyInput('email');
    }

    public function index() {
        $user = auth()->user();
        return view('users.edit', ['user' => $user]);
    }

    public function update(Request $request) {
        $user = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|string'
        ]);

        if (!Hash::check($user['password'], auth()->user()->password)) {
            return back()
                ->withErrors(['password' => 'Please check your credentials.']);
        }

        unset($user['password']);

        User::where('id', auth()->id())->update($user);

        return redirect('/')->with('message', 'You have updated your profile!');
    }
}
