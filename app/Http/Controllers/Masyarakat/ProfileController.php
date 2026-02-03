<?php

namespace App\Http\Controllers\Masyarakat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->user_type !== 'masyarakat_umum') {
            abort(403, 'Unauthorized access.');
        }

        $totalSubmissions = $this->getTotalSubmissions($user);
        $completedSubmissions = $this->getCompletedSubmissions($user);

        return view('masyarakat.profile', compact('totalSubmissions', 'completedSubmissions'));
    }

    private function getTotalSubmissions($user)
    {
        $total = 0;

        if (class_exists('App\Models\Submission')) {
            $total += \App\Models\Submission::where('user_id', $user->id)->count();
        }

        if (class_exists('App\Models\Consultation')) {
            $total += \App\Models\Consultation::where('user_id', $user->id)->count();
        }

        if (class_exists('App\Models\Complaint')) {
            $total += \App\Models\Complaint::where('user_id', $user->id)->count();
        }

        return $total;
    }

    private function getCompletedSubmissions($user)
    {
        $total = 0;

        if (class_exists('App\Models\Submission')) {
            $total += \App\Models\Submission::where('user_id', $user->id)
                ->whereIn('status', ['completed', 'selesai', 'approved'])
                ->count();
        }

        if (class_exists('App\Models\Consultation')) {
            $total += \App\Models\Consultation::where('user_id', $user->id)
                ->whereIn('status', ['completed', 'selesai', 'approved'])
                ->count();
        }

        if (class_exists('App\Models\Complaint')) {
            $total += \App\Models\Complaint::where('user_id', $user->id)
                ->whereIn('status', ['completed', 'selesai', 'approved'])
                ->count();
        }

        return $total;
    }

    /**
     * ✅ UPDATE FOTO PROFIL -> simpan ke Supabase (biar Railway & local sama-sama aman)
     */
    public function updatePhoto(Request $request)
    {
        try {
            $request->validate([
                'profile_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ], [
                'profile_photo.required' => 'Foto profil wajib dipilih.',
                'profile_photo.image' => 'File harus berupa gambar.',
                'profile_photo.mimes' => 'Format foto harus JPEG, PNG, atau JPG.',
                'profile_photo.max' => 'Ukuran foto maksimal 2MB.',
            ]);

            $user = auth()->user();

            if (!$request->hasFile('profile_photo')) {
                return back()->with('photo_error', 'File foto tidak ditemukan.');
            }

            $file = $request->file('profile_photo');

            if (!$file->isValid()) {
                return back()->with('photo_error', 'File foto tidak valid.');
            }

            /**
             * HAPUS FOTO LAMA:
             * - kalau dulu tersimpan local (public disk) -> hapus local
             * - kalau sudah supabase path -> hapus supabase (kalau mau)
             */
            if ($user->profile_photo) {
                $old = $user->profile_photo;

                // old local path
                if (Str::startsWith($old, ['profile-photos/', 'public/', 'storage/'])) {
                    $normalized = $old;
                    if (Str::startsWith($normalized, 'public/')) $normalized = Str::after($normalized, 'public/');
                    if (Str::startsWith($normalized, 'storage/')) $normalized = Str::after($normalized, 'storage/');
                    if (Storage::disk('public')->exists($normalized)) {
                        Storage::disk('public')->delete($normalized);
                    }
                } else {
                    // old supabase path (opsional delete)
                    try {
                        Storage::disk('supabase_ktp')->delete(ltrim($old, '/'));
                    } catch (\Exception $e) {
                        // ga fatal
                    }
                }
            }

            // ✅ simpan ke supabase bucket profile photos
            // PAKAI disk supabase_ktp biar ga nambah disk baru (sesuai request "tanpa helper" & minim ubah config)
            // bucket dikontrol lewat SUPABASE_KTP_BUCKET? -> kalau kamu mau bucket khusus profile, bikin disk baru.
            // Untuk aman: kita taruh di folder "profile-photos/"
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $path = 'profile-photos/' . $user->id . '/' . $filename;

            Storage::disk('supabase_ktp')->put(
                $path,
                file_get_contents($file)
            );

            // simpan path supabase ke DB
            $user->profile_photo = $path;
            $user->save();

            return back()->with('photo_success', 'Foto profil berhasil diperbarui!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessage = $e->validator->errors()->first('profile_photo');
            return back()->with('photo_error', $errorMessage);
        } catch (\Exception $e) {
            Log::error('Profile photo upload failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return back()->with('photo_error', 'Gagal mengupload foto profil.');
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required',
                'password' => 'required|min:8|confirmed',
            ], [
                'current_password.required' => 'Password saat ini wajib diisi.',
                'password.required' => 'Password baru wajib diisi.',
                'password.min' => 'Password minimal 8 karakter.',
                'password.confirmed' => 'Konfirmasi password tidak cocok.',
            ]);

            $user = auth()->user();

            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('password_error', 'Password saat ini tidak sesuai.');
            }

            if (Hash::check($request->password, $user->password)) {
                return back()->with('password_error', 'Password baru harus berbeda dari password lama.');
            }

            $user->password = Hash::make($request->password);
            $user->save();

            return back()->with('password_success', 'Password berhasil diubah!');

        } catch (\Exception $e) {
            return back()->with('password_error', 'Gagal mengubah password.');
        }
    }
}