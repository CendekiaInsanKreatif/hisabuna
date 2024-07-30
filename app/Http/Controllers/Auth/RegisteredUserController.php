<?php

namespace App\Http\Controllers\Auth;

use Imagick;
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

        // da($request->all());

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'no_telp' => $request->no_telp,
            'password' => Hash::make($request->password),
            'roles' => $roles,
            'company_name' => $request->company_name,
        ]);

        if ($request->hasFile('image')) {
            $lampiranFile = $request->file('image');
            $filePath = 'profiles/' . $user->company_name;
            $fileName = $user->id . '.' . $lampiranFile->getClientOriginalExtension();
            $tempPath = $lampiranFile->getPathName();
        
            try {
                // Buat instance baru dari Imagick
                $imagick = new Imagick($tempPath);
        
                // Atur kualitas kompresi (nilai antara 0 hingga 100, 75 adalah keseimbangan yang baik)
                $imagick->setImageCompressionQuality(30);
        
                // Tentukan path untuk menyimpan gambar yang telah dikompresi
                $compressedImagePath = storage_path('app/public/' . $filePath . '/' . $fileName);
        
                // Buat direktori jika belum ada
                $directoryPath = storage_path('app/public/' . $filePath);
                if (!file_exists($directoryPath)) {
                    mkdir($directoryPath, 0755, true);
                }
        
                // Simpan gambar yang telah dikompresi
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

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
