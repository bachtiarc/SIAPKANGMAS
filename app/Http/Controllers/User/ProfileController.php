<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Display the user profile.
     */
    public function index()
    {
        $user = auth()->user();

        // Get statistics (same as dashboard)
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
            // Validate file
            $request->validate([
                'profile_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ], [
                'profile_photo.required' => 'Foto profil wajib dipilih.',
                'profile_photo.image' => 'File harus berupa gambar.',
                'profile_photo.mimes' => 'Format foto harus jpeg, png, atau jpg.',
                'profile_photo.max' => 'Ukuran foto maksimal 2MB.',
            ]);

            $user = auth()->user();

            // Check if file was uploaded
            if (!$request->hasFile('profile_photo')) {
                Log::error('Profile photo upload failed: No file in request');
                return back()->with('photo_error', 'File foto tidak ditemukan. Silakan pilih foto terlebih dahulu.');
            }

            $file = $request->file('profile_photo');

            // Check if file is valid
            if (!$file->isValid()) {
                Log::error('Profile photo upload failed: Invalid file', [
                    'error' => $file->getError(),
                    'error_message' => $file->getErrorMessage()
                ]);
                return back()->with('photo_error', 'File foto tidak valid atau rusak. Error: ' . $file->getErrorMessage());
            }

            // Log file info for debugging
            Log::info('Uploading profile photo', [
                'user_id' => $user->id,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);

            // Delete old photo if exists
            if ($user->profile_photo) {
                if (Storage::disk('public')->exists($user->profile_photo)) {
                    Storage::disk('public')->delete($user->profile_photo);
                    Log::info('Deleted old profile photo', ['path' => $user->profile_photo]);
                }
            }

            // Store new photo
            $path = $file->store('profile-photos', 'public');

            if (!$path) {
                Log::error('Profile photo upload failed: Storage returned false');
                return back()->with('photo_error', 'Gagal menyimpan foto ke storage. Periksa permission folder storage/app/public.');
            }

            // Verify file was actually saved
            if (!Storage::disk('public')->exists($path)) {
                Log::error('Profile photo upload failed: File not found after save', ['path' => $path]);
                return back()->with('photo_error', 'Foto tidak ditemukan setelah disimpan. Periksa konfigurasi storage.');
            }

            // Update user profile_photo field
            $user->profile_photo = $path;
            $user->save();

            Log::info('Profile photo updated successfully', [
                'user_id' => $user->id,
                'path' => $path
            ]);

            return back()->with('photo_success', 'Foto profil berhasil diperbarui!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation error - let Laravel handle it
            throw $e;
        } catch (\Exception $e) {
            Log::error('Profile photo upload failed with exception', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('photo_error', 'Gagal mengupload foto profil. Error: ' . $e->getMessage());
        }
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
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

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('password_error', 'Password saat ini tidak sesuai.');
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        Log::info('User password updated', ['user_id' => $user->id]);

        return back()->with('password_success', 'Password berhasil diubah!');
    }
}