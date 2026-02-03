<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Display the user profile.
     */
    public function index()
    {
        $user = auth()->user();

        $totalSubmissions = $this->getTotalSubmissions($user);
        $completedSubmissions = $this->getCompletedSubmissions($user);

        return view('user.profile', compact('totalSubmissions', 'completedSubmissions'));
    }

    /**
     * Get total submissions for user
     */
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

    /**
     * Get completed submissions
     */
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
     * Update the user's profile photo.
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
            $file = $request->file('profile_photo');

            if (!$file || !$file->isValid()) {
                return back()->with('photo_error', 'File foto tidak valid.');
            }

            Log::info('Uploading profile photo (Supabase)', [
                'user_id' => $user->id,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);

            // 🧹 HAPUS FOTO LAMA DI SUPABASE
            if ($user->profile_photo) {
                try {
                    Storage::disk('supabase_profile')->delete($user->profile_photo);
                } catch (\Exception $e) {
                    // tidak fatal
                }
            }

            // 📦 SIMPAN KE SUPABASE bucket profile-photos
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

            // 💾 SIMPAN PATH ke DB (BUKAN URL)
            $user->profile_photo = $path;
            $user->save();

            Log::info('Profile photo updated successfully (Supabase)', [
                'user_id' => $user->id,
                'path' => $path,
            ]);

            return back()->with('photo_success', 'Foto profil berhasil diperbarui!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('photo_error', $e->validator->errors()->first('profile_photo'));
        } catch (\Exception $e) {
            Log::error('Profile photo upload failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return back()->with('photo_error', 'Gagal mengupload foto profil.');
        }
    }

    /**
     * Update the user's password.
     */
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

            Log::info('User password updated', ['user_id' => $user->id]);

            return back()->with('password_success', 'Password berhasil diubah!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessage = $e->validator->errors()->first();
            return back()->with('password_error', $errorMessage);
        } catch (\Exception $e) {
            Log::error('Password update failed with exception', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);
            
            return back()->with('password_error', 'Gagal mengubah password. Silakan coba lagi.');
        }
    }
}