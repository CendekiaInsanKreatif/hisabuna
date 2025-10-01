<?php

namespace App\Http\Controllers\Auth;

use Imagick;
use Carbon\Carbon;
use Spatie\ImageOptimizer\OptimizerChainFactory;
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
            'no_hp'     => ['required', 'string', 'max:255'],
            'no_telp'   => ['required', 'string', 'max:255'],
            'password'  => ['required', 'confirmed', Rules\Password::defaults()],
            'image'     => ['required', 'image', 'mimes:jpg,jpeg,png'],
        ]);

        $user = User::all();

        $roles = $user->count() == 0 ? 'superadmin' : 'user';

        $name = $request->name;
        if (!preg_match('/^[a-zA-Z ]+$/', $name)) {
            return redirect()->back()->withErrors(['name' => 'Hanya karakter alfabet dan spasi yang diperbolehkan.'])->withInput();
        }
        $email = $request->email;
        if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
            return redirect()->back()->withErrors(['email' => 'Format email tidak valid.'])->withInput();
        }
        $no_hp = $request->no_hp;
        if (!preg_match('/^\+?[0-9]{10,15}$/', $no_hp)) {
            return redirect()->back()->withErrors(['no_hp' => 'Format nomor HP tidak valid.'])->withInput();
        }
        $no_telp = $request->no_telp;
        if (!preg_match('/^\+?[0-9\s\-()]{10,20}$/', $no_telp)) {
            return redirect()->back()->withErrors(['no_telp' => 'Format nomor telepon tidak valid.'])->withInput();
        }
        $company_name = $request->company_name;
        if (!preg_match('/^[a-zA-Z ]+$/', $company_name)) {
            return redirect()->back()->withErrors(['name' => 'Hanya karakter alfabet dan spasi yang diperbolehkan.'])->withInput();
        }
        $password = $request->password;
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#@!])[A-Za-z\d#@!]{8,}$/', $password)) {
            return redirect()->back()->withErrors(['password' => 'Password harus memiliki minimal 8 karakter, terdapat huruf besar, huruf kecil, angka, dan hanya diperbolehkan simbol #@!'])->withInput();
        }


        $user = User::create([
            'name'          => $name,
            'email'         => $email,
            'no_hp'         => $no_hp,
            'no_telp'       => $no_telp,
            'periode'       => date('Y'),
            'password'      => Hash::make($password),
            'roles'         => $roles,
            'company_name'  => $company_name,
            'is_active'     => "1",
            'is_deleted'    => "0",
            'profile'       => "trial",
	        'trial_ends_at'    => Carbon::now()->addDays(180), //6 bulan
        ]);

        if ($request->hasFile('image')) {
            $lampiranFile = $request->file('image');
            $fileExtension = $lampiranFile->getClientOriginalExtension();
            if (!preg_match('/^(jpg|jpeg|png)$/i', $fileExtension)) {
                return redirect()->back()->withErrors(['image' => 'Hanya file dengan ekstensi JPG, JPEG, atau PNG yang diperbolehkan.'])->withInput();
            }
            $filePath = 'profiles/' . $user->company_name;
            $fileName = $user->id . '.' . $lampiranFile->getClientOriginalExtension();
            $tempPath = $lampiranFile->getPathName();

            try {
                $imagick = new Imagick($tempPath);
                $imagick->setImageCompressionQuality(30);
                $compressedImagePath = storage_path('app/public/' . $filePath . '/' . $fileName);
                $directoryPath = storage_path('app/public/' . $filePath);
                if (!file_exists($directoryPath)) {
                    mkdir($directoryPath, 0755, true);
                }
                $imagick->writeImage($compressedImagePath);

                // Hapus objek Imagick dari memori
                $imagick->clear();
                $imagick->destroy();

                // Simpan path gambar yang telah dikompresi ke database
                $user->company_logo = $filePath . '/' . $fileName;
                $user->save();
            } catch (ImagickException $e) {
                // Tangani kesalahan jika ada masalah dengan Imagick
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        event(new Registered($user));

        // Auth::login($user);

        return redirect(route('login', absolute: true));
    }
}
