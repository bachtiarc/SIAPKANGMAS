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

        {{-- VALIDATION ERRORS --}}
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
        <form method="POST"
              action="{{ route('register.submit') }}"
              enctype="multipart/form-data"
              class="space-y-6">
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

            <!-- PEKERJAAN -->
            <div>
                <label class="font-semibold">Pekerjaan</label>
                <select id="pekerjaan" name="pekerjaan" required
                        class="w-full mt-1 border rounded-lg px-4 py-2">
                    <option value="">Pilih Pekerjaan</option>
                    <option value="PNS" {{ old('pekerjaan')=='PNS'?'selected':'' }}>PNS</option>
                    <option value="Wiraswasta" {{ old('pekerjaan')=='Wiraswasta'?'selected':'' }}>Wiraswasta</option>
                    <option value="Pelaku UMKM" {{ old('pekerjaan')=='Pelaku UMKM'?'selected':'' }}>Pelaku UMKM</option>
                    <option value="Mahasiswa" {{ old('pekerjaan')=='Mahasiswa'?'selected':'' }}>Mahasiswa</option>
                    <option value="Ibu Rumah Tangga" {{ old('pekerjaan')=='Ibu Rumah Tangga'?'selected':'' }}>Ibu Rumah Tangga</option>
                    <option value="Lainnya" {{ old('pekerjaan')=='Lainnya'?'selected':'' }}>Lainnya</option>
                </select>
            </div>

            <!-- PEKERJAAN LAINNYA -->
            <div id="pekerjaan_lainnya_wrapper"
                 class="{{ old('pekerjaan')=='Lainnya' ? '' : 'hidden' }}">
                <label class="font-semibold">Pekerjaan Lainnya</label>
                <input type="text"
                       id="pekerjaan_lainnya"
                       name="pekerjaan_lainnya"
                       value="{{ old('pekerjaan_lainnya') }}"
                       placeholder="Tulis pekerjaan Anda"
                       class="w-full mt-1 border rounded-lg px-4 py-2">
            </div>

            <!-- WILAYAH -->
            <div>
                <label class="font-semibold">Provinsi</label>
                <select id="provinsi" name="provinsi" required
                        class="w-full mt-1 border rounded-lg px-4 py-2">
                    <option value="">Pilih Provinsi</option>
                </select>
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

<!-- MODAL VERIFIKASI EMAIL (INLINE, BUKAN PARTIAL) -->
@if(session('show_verification_modal'))
<div id="verificationModal"
     class="fixed inset-0 bg-black/40 flex items-center justify-center z-[9000]">
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
            Email verifikasi telah dikirim ke
            <strong>{{ session('registered_email') }}</strong>.
            Silakan klik link di email tersebut untuk mengaktifkan akun.
        </p>

        <form method="POST" action="{{ route('resend.verification') }}">
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
@endif

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


document.getElementById('pekerjaan')?.addEventListener('change', function () {
    const wrapper = document.getElementById('pekerjaan_lainnya_wrapper');
    const input = document.getElementById('pekerjaan_lainnya');

    if (this.value === 'Lainnya') {
        wrapper.classList.remove('hidden');
        input.setAttribute('required', 'required');
    } else {
        wrapper.classList.add('hidden');
        input.removeAttribute('required');
        input.value = '';
    }
});


const provinsi  = document.getElementById('provinsi');
const kabupaten = document.getElementById('kabupaten');
const kecamatan = document.getElementById('kecamatan');
const desa      = document.getElementById('desa');

const oldProv = @json(old('provinsi'));
const oldKab  = @json(old('kabupaten'));
const oldKec  = @json(old('kecamatan'));
const oldDes  = @json(old('desa'));

fetch('/api/provinsi')
    .then(r => r.json())
    .then(d => {
        d.forEach(x => {
            const selected = String(x.kode) === String(oldProv) ? 'selected' : '';
            provinsi.innerHTML += `<option value="${x.kode}" ${selected}>${x.nama}</option>`;
        });
        if (provinsi.value) {
            provinsi.dispatchEvent(new Event('change'));
        }
    });

provinsi.onchange = e => {
    kabupaten.innerHTML = '<option value="">Pilih Kabupaten / Kota</option>';
    kecamatan.innerHTML = '<option value="">Pilih Kecamatan</option>';
    desa.innerHTML      = '<option value="">Pilih Desa</option>';

    if (!e.target.value) return;

    fetch(`/api/kabupaten/${e.target.value}`)
        .then(r => r.json())
        .then(d => {
            d.forEach(x => {
                const selected = String(x.kode) === String(oldKab) ? 'selected' : '';
                kabupaten.innerHTML += `<option value="${x.kode}" ${selected}>${x.nama}</option>`;
            });

            if (kabupaten.value) {
                kabupaten.dispatchEvent(new Event('change'));
            }
        });
};

kabupaten.onchange = e => {
    kecamatan.innerHTML = '<option value="">Pilih Kecamatan</option>';
    desa.innerHTML      = '<option value="">Pilih Desa</option>';

    if (!e.target.value) return;

    fetch(`/api/kecamatan/${e.target.value}`)
        .then(r => r.json())
        .then(d => {
            d.forEach(x => {
                const selected = String(x.kode) === String(oldKec) ? 'selected' : '';
                kecamatan.innerHTML += `<option value="${x.kode}" ${selected}>${x.nama}</option>`;
            });

            if (kecamatan.value) {
                kecamatan.dispatchEvent(new Event('change'));
            }
        });
};

kecamatan.onchange = e => {
    desa.innerHTML = '<option value="">Pilih Desa</option>';

    if (!e.target.value) return;

    fetch(`/api/desa/${e.target.value}`)
        .then(r => r.json())
        .then(d => {
            d.forEach(x => {
                const selected = String(x.kode) === String(oldDes) ? 'selected' : '';
                desa.innerHTML += `<option value="${x.kode}" ${selected}>${x.nama}</option>`;
            });
        });
};
</script>
@endpush