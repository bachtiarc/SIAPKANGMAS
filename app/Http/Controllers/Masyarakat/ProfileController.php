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
            ]);

            $user = auth()->user();
            $file = $request->file('profile_photo');

            if (!$file || !$file->isValid()) {
                return back()->with('photo_error', 'File foto tidak valid.');
            }

            // 🧹 hapus foto lama (jika ada)
            if ($user->profile_photo) {
                try {
                    Storage::disk('supabase_profile')->delete($user->profile_photo);
                } catch (\Exception $e) {
                    // tidak fatal
                }
            }

            // 📦 simpan ke Supabase bucket profile-photos
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = "users/{$user->id}/{$filename}";

            Storage::disk('supabase_profile')->put(
                $path,
                file_get_contents($file),
                [
                    'visibility' => 'public',
                    'ContentType' => $file->getMimeType(),
                ]
            );

            // 💾 simpan PATH (bukan URL) ke DB
            $user->profile_photo = $path;
            $user->save();

            return back()->with('photo_success', 'Foto profil berhasil diperbarui!');

        } catch (\Exception $e) {
            Log::error('Upload profile photo failed', [
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