@extends('layouts.app')

@section('title', 'Registrasi Akun Masyarakat')

@push('styles')
<style>
@keyframes slideIn {
    from { transform: translateX(400px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
@keyframes slideOut {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(400px); opacity: 0; }
}
.toast-enter { animation: slideIn .3s ease-out forwards; }
.toast-exit { animation: slideOut .3s ease-in forwards; }
</style>
@endpush

@section('content')

<div id="toast-container" class="fixed top-6 right-6 z-[9999]"></div>

<!-- PAGE -->
<div class="min-h-screen bg-gradient-to-br from-white via-blue-50 to-white flex items-center justify-center py-16 px-4">
    <div class="w-full max-w-3xl bg-white/90 backdrop-blur rounded-3xl shadow-xl p-10">

        <!-- HEADER -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold text-gray-900 mb-3">
                Registrasi Akun Masyarakat
            </h1>
            <p class="text-gray-600">
                Layanan ini khusus untuk masyarakat yang ingin mengajukan permohonan,
                konsultasi, atau pengaduan ke Disperindag Provinsi Jawa Tengah.
            </p>
        </div>

        {{-- VALIDATION ERRORS AS TOAST --}}
        @if ($errors->any())
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    @foreach ($errors->all() as $e)
                        showToast(@json($e), 'error');
                    @endforeach
                });
            </script>
        @endif

        <!-- FORM -->
        <form method="POST" action="{{ route('register.submit') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label class="font-semibold">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full mt-1 border rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="font-semibold">NIK</label>
                <input type="text" name="nik" maxlength="16" value="{{ old('nik') }}" required
                       class="w-full mt-1 border rounded-lg px-4 py-2">
                <p class="text-xs text-gray-500 mt-1">16 digit angka</p>
            </div>

            <div>
                <label class="font-semibold">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full mt-1 border rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="font-semibold">Nomor Telepon</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required
                       class="w-full mt-1 border rounded-lg px-4 py-2">
                <p class="text-xs text-gray-500 mt-1">Format: 08…, 62…, atau +62…</p>
            </div>

            <div>
                <label class="font-semibold">Kabupaten / Kota</label>
                <select id="kabupaten" name="kabupaten" required
                        class="w-full mt-1 border rounded-lg px-4 py-2">
                    <option value="">Pilih Kabupaten / Kota</option>
                </select>
            </div>

            <div>
                <label class="font-semibold">Kecamatan</label>
                <select id="kecamatan" name="kecamatan" required
                        class="w-full mt-1 border rounded-lg px-4 py-2">
                    <option value="">Pilih Kecamatan</option>
                </select>
            </div>

            <div>
                <label class="font-semibold">Desa / Kelurahan</label>
                <select id="desa" name="desa" required
                        class="w-full mt-1 border rounded-lg px-4 py-2">
                    <option value="">Pilih Desa</option>
                </select>
            </div>

            <div>
                <label class="font-semibold">Alamat Lengkap</label>
                <textarea name="alamat" rows="3" required
                          class="w-full mt-1 border rounded-lg px-4 py-2">{{ old('alamat') }}</textarea>
            </div>

            <div>
                <label class="font-semibold">Foto KTP</label>
                <input type="file" id="foto_ktp" name="foto_ktp" accept="image/*" required>
                <p class="text-xs text-gray-500 mt-1">JPG/JPEG/PNG — Maks 2MB</p>
            </div>

            <div>
                <label class="font-semibold">Password</label>
                <input type="password" name="password" required
                       class="w-full mt-1 border rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="font-semibold">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required
                       class="w-full mt-1 border rounded-lg px-4 py-2">
            </div>

            <button type="submit"
                    class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 rounded-lg">
                Daftar Sekarang
            </button>
        </form>
    </div>
</div>

<!-- MODAL VERIFIKASI EMAIL -->
<div id="verificationModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-[9000]">
    <div class="bg-white rounded-2xl max-w-md w-full p-8 text-center">

        <p class="text-sm text-gray-400 mb-4">VERIFIKASI EMAIL</p>

        <div class="w-24 h-24 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
            <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 8l9 6 9-6M4 6h16a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2z"/>
            </svg>
        </div>

        <h2 class="text-xl font-bold mb-2">Cek Email Anda</h2>

        <p class="text-gray-600 mb-6">
            Silakan cek inbox atau folder spam Anda dan klik link verifikasi untuk mengaktifkan akun.
            Email verifikasi telah dikirim ke
            <strong>{{ session('registered_email') }}</strong>
        </p>

        <form id="resendForm" method="POST" action="{{ route('resend.verification') }}">
            @csrf
            <input type="hidden" name="email" value="{{ session('registered_email') }}">
            <button type="submit"
                    class="w-full bg-blue-700 hover:bg-blue-800 text-white py-3 rounded-lg font-semibold mb-3">
                Kirim Ulang Email Verifikasi
            </button>
        </form>

        <a href="{{ route('login') }}"
           class="block w-full border border-gray-300 py-3 rounded-lg font-semibold text-gray-700">
            Kembali ke Halaman Login
        </a>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showToast(msg, type='success') {
    const c = document.getElementById('toast-container');
    const color = type === 'error'
        ? 'border-red-500 text-red-600'
        : 'border-green-500 text-green-600';

    const t = document.createElement('div');
    t.className = `toast-enter bg-white border-l-4 ${color} shadow rounded-lg p-4 mb-3 min-w-[320px]`;
    t.innerText = msg;

    c.appendChild(t);

    setTimeout(() => {
        t.classList.replace('toast-enter','toast-exit');
        setTimeout(() => t.remove(), 300);
    }, 4000);
}

document.getElementById('foto_ktp')?.addEventListener('change', e => {
    if (e.target.files[0]?.size > 2 * 1024 * 1024) {
        showToast('Ukuran file maksimal 2MB', 'error');
        e.target.value = '';
    }
});

fetch('/api/kabupaten').then(r=>r.json()).then(d=>{
    d.forEach(x=>kabupaten.innerHTML+=`<option value="${x.kode}">${x.nama}</option>`);
});

kabupaten.onchange=e=>{
    fetch(`/api/kecamatan/${e.target.value}`).then(r=>r.json()).then(d=>{
        kecamatan.innerHTML='<option value="">Pilih Kecamatan</option>';
        desa.innerHTML='<option value="">Pilih Desa</option>';
        d.forEach(x=>kecamatan.innerHTML+=`<option value="${x.kode}">${x.nama}</option>`);
    });
};

kecamatan.onchange=e=>{
    fetch(`/api/desa/${e.target.value}`).then(r=>r.json()).then(d=>{
        desa.innerHTML='<option value="">Pilih Desa</option>';
        d.forEach(x=>desa.innerHTML+=`<option value="${x.kode}">${x.nama}</option>`);
    });
};

document.addEventListener('DOMContentLoaded',()=>{
    @if(session('show_verification_modal'))
        verificationModal.classList.remove('hidden');
        verificationModal.classList.add('flex');
    @endif

    resendForm?.addEventListener('submit',()=>{
        showToast('Email verifikasi berhasil dikirim ulang','success');
    });
});
</script>
@endpush