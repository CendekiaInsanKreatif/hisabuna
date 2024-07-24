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
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'  => ['required', 'confirmed', Rules\Password::defaults()],
            'image'     => ['required', 'image', 'mimes:jpg,jpeg,png'],
        ]);

        $user = User::all();

        if($user->count() == 0){
            $roles = 'superadmin';
        }else{
            $roles = 'user';
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'roles' => $roles,
            'company_name' => $request->company_name,
        ]);

        if ($request->hasFile('image')) {
            $lampiranFile = $request->file('image');
            $filePath = 'profiles/' . $user->company_name;
            $fileName = $user->id . '.' . $lampiranFile->getClientOriginalExtension();
            $lampiranFile->storeAs($filePath, $fileName, 'public');
            $user->company_logo = $filePath . '/' . $fileName;
            $user->save();
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
