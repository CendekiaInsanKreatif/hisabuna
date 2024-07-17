<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Models\Company;


class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load('company');
        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        da($request->all());
        $user = $request->user();

        $validatedData = $request->validated();
        $data = $request->all();

        if ($request->hasFile('profile')) {
            $profileImage = $request->file('profile');
            $profileImageName = $user->id . '.' . $profileImage->getClientOriginalExtension();
            $profileImagePath = $profileImage->storeAs('profiles', $profileImageName, 'public');
            $data['profile'] = $profileImagePath;
        }

        $user->fill([
            'name' => $data['name'],
            'email' => $data['email'],
            'profile' => $data['profile'] ?? $user->profile,
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($user->company) {
            $user->company->update([
                'name' => $data['company_name'],
                'desc' => $data['company_desc'],
                'logo' => $data['logo'] ?? $user->company->logo,
            ]);
        } else {
            $company = Company::create([
                'name' => $data['company_name'],
                'desc' => $data['company_desc'],
                'logo' => $data['logo'],
            ]);

            $user->company_id = $company->id;
            $user->save();
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }





    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
