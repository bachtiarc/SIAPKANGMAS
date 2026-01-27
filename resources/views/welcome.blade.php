@extends('layouts.app')

@section('title', 'Beranda')

@section('content')

<style>
    html { scroll-behavior: smooth; }

    :root{
        --ease-out: cubic-bezier(.16, 1, .3, 1);
        --ease-spring: cubic-bezier(.2, .9, .2, 1.1);
        --dur-1: .18s;
        --dur-2: .35s;
        --dur-3: .7s;

        --blue: 37 99 235;   
        --orange: 249 115 22; 
    }

    body{
        text-rendering: optimizeLegibility;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    .soft-noise { position: relative; }
    .soft-noise::after{
        content:"";
        position:absolute; inset:0;
        pointer-events:none;
        opacity:.06;
        mix-blend-mode:multiply;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.8' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='160' height='160' filter='url(%23n)' opacity='.4'/%3E%3C/svg%3E");
        background-size: 160px 160px;
        border-radius: inherit;
    }

    .section-glow{
        position: relative;
        overflow: hidden;
        isolation: isolate;
    }
    .section-glow::before{
        content:"";
        position:absolute;
        inset:-90px -120px;
        background:
            radial-gradient(420px 240px at 18% 12%, rgba(var(--blue), .12), transparent 55%),
            radial-gradient(420px 240px at 82% 18%, rgba(var(--orange), .12), transparent 55%),
            radial-gradient(520px 280px at 50% 100%, rgba(99,102,241,.10), transparent 60%);
        filter: blur(2px);
        pointer-events:none;
        z-index:0;
    }
    .section-inner{ position: relative; z-index:1; }

    .wave{
        position:absolute;
        left:0; right:0;
        height:92px;
        pointer-events:none;
        filter: drop-shadow(0 -10px 20px rgba(15, 23, 42, .06));
    }
    .wave svg{ width:100%; height:100%; display:block; }

    .reveal{
        opacity:0;
        transform: translateY(18px) scale(.995);
        filter: blur(6px);
        transition:
            opacity var(--dur-3) var(--ease-out),
            transform var(--dur-3) var(--ease-out),
            filter var(--dur-3) var(--ease-out);
        will-change: opacity, transform, filter;
    }
    .reveal.show{
        opacity:1;
        transform: translateY(0) scale(1);
        filter: blur(0);
    }

    .premium-hover{
        transition:
            transform var(--dur-2) var(--ease-out),
            box-shadow var(--dur-2) var(--ease-out),
            filter var(--dur-2) var(--ease-out);
        will-change: transform, box-shadow;
    }
    .premium-hover:hover{
        transform: translateY(-2px);
        box-shadow:
            0 18px 45px rgba(15, 23, 42, .10),
            0 6px 18px rgba(15, 23, 42, .06);
    }
    .premium-hover:active{ transform: translateY(0); }

    .btn-breathe{
        transition: transform var(--dur-2) var(--ease-out), box-shadow var(--dur-2) var(--ease-out), filter var(--dur-2) var(--ease-out);
        will-change: transform;
    }
    .btn-breathe:hover{ transform: translateY(-1px); }
    .btn-breathe:active{ transform: translateY(0); }

    .focus-ring:focus{
        outline:none !important;
        box-shadow: 0 0 0 4px rgba(var(--blue), .18);
        border-color: rgba(var(--blue), .6) !important;
    }
    input[type="text"], input[type="email"], textarea{
        transition: border-color var(--dur-2) var(--ease-out), box-shadow var(--dur-2) var(--ease-out), background-color var(--dur-2) var(--ease-out);
    }
    textarea{ resize: vertical; }

    #quickTrackModal{
        opacity:0;
        pointer-events:none;
        transition: opacity var(--dur-2) var(--ease-out);
    }
    #quickTrackModal.is-open{
        opacity:1;
        pointer-events:auto;
    }
    #quickTrackBackdrop{
        opacity:0;
        transition: opacity var(--dur-2) var(--ease-out);
    }
    #quickTrackModal.is-open #quickTrackBackdrop{ opacity:1; }

    .modal-enter{ opacity:0; transform: translateY(10px) scale(.98); filter: blur(6px); }
    .modal-enter-active{
        opacity:1; transform: translateY(0) scale(1); filter: blur(0);
        transition: all .32s var(--ease-out);
    }
    .modal-leave{ opacity:1; transform: translateY(0) scale(1); filter: blur(0); }
    .modal-leave-active{
        opacity:0; transform: translateY(10px) scale(.98); filter: blur(6px);
        transition: all .24s var(--ease-out);
    }

    @media (prefers-reduced-motion: reduce){
        html{ scroll-behavior:auto; }
        .reveal, .premium-hover, .btn-breathe,
        #quickTrackModal, #quickTrackBackdrop,
        .modal-enter-active, .modal-leave-active{
            transition:none !important;
            transform:none !important;
            filter:none !important;
        }
    }
</style>

<!-- HERO -->
<div class="relative overflow-hidden bg-white pb-10 section-glow soft-noise">
    <div class="pointer-events-none absolute -top-24 -left-24 h-72 w-72 rounded-full bg-blue-200 blur-3xl opacity-50"></div>
    <div class="pointer-events-none absolute -top-10 -right-24 h-72 w-72 rounded-full bg-orange-200 blur-3xl opacity-50"></div>
    <div class="pointer-events-none absolute bottom-0 left-1/2 h-72 w-72 -translate-x-1/2 rounded-full bg-indigo-200 blur-3xl opacity-40"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20 relative section-inner">
        <div class="text-center reveal">
            <h1 class="mt-6 text-4xl sm:text-5xl font-bold text-gray-900 mb-4 leading-tight">
                Selamat Datang di
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-blue-400">SIAP</span><span class="bg-clip-text text-transparent bg-gradient-to-r from-orange-500 to-orange-400">KANGMAS</span>
            </h1>

            <p class="text-lg text-gray-600 mb-10 max-w-3xl mx-auto">
                Layanan bantuan terintegrasi untuk mendukung konsultasi, pengaduan, dan permohonan informasi Anda
                di Dinas Perindustrian dan Perdagangan Jawa Tengah.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-3 sm:gap-4">
                <a href="{{ route('register') }}"
                   class="btn-breathe group inline-flex items-center justify-center bg-gradient-to-r from-orange-500 to-orange-400 hover:from-orange-600 hover:to-orange-500 text-white px-8 py-3 rounded-xl font-semibold transition shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Ajukan Tiket Baru
                </a>

                <button type="button"
                        id="openQuickTrack"
                        class="btn-breathe group inline-flex items-center justify-center px-8 py-3 rounded-xl font-semibold transition
                               bg-white border border-gray-200 hover:border-blue-200 hover:bg-blue-50 text-blue-700 shadow-sm hover:shadow">
                    <svg class="w-5 h-5 mr-2 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Lacak Tiket Cepat
                </button>
            </div>

            <div class="mt-10 flex flex-wrap items-center justify-center gap-3 text-sm text-gray-600">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gray-50 border border-gray-200 premium-hover">
                    <span class="h-2 w-2 rounded-full bg-blue-500"></span> Konsultasi
                </span>
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gray-50 border border-gray-200 premium-hover">
                    <span class="h-2 w-2 rounded-full bg-red-500"></span> Pengaduan
                </span>
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-gray-50 border border-gray-200 premium-hover">
                    <span class="h-2 w-2 rounded-full bg-orange-500"></span> Permohonan Informasi
                </span>
            </div>
        </div>
    </div>

    <div class="wave bottom-0 translate-y-full">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
            <path fill="rgb(219 234 254)" d="M0,64 C240,120 480,0 720,56 C960,112 1200,96 1440,32 L1440,120 L0,120 Z"></path>
        </svg>
    </div>
</div>

<!-- FEATURES -->
<div class="relative bg-blue-100 py-16 pb-24 section-glow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 section-inner">
        <div class="reveal">
            <h2 class="text-3xl font-bold text-center text-black mb-4">
                Fitur Utama Layanan Kami
            </h2>
            <p class="text-center text-black/90 mb-12">
                Kami menyediakan berbagai fitur untuk mempermudah Anda mendapatkan bantuan yang dibutuhkan.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
            <div class="reveal bg-white/95 backdrop-blur rounded-2xl p-8 shadow-md hover:shadow-xl transition border border-white/40 hover:-translate-y-0.5 premium-hover">
                <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mb-6 mx-auto">
                    <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-center text-gray-900 mb-3">Ajukan Permohonan</h3>
                <p class="text-center text-gray-600">
                    Buat tiket konsultasi, pengaduan, dan permohonan informasi melalui formulir online.
                </p>
            </div>

            <div class="reveal bg-white/95 backdrop-blur rounded-2xl p-8 shadow-md hover:shadow-xl transition border border-white/40 hover:-translate-y-0.5 premium-hover">
                <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6 mx-auto">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-center text-gray-900 mb-3">Lacak Tiket</h3>
                <p class="text-center text-gray-600">
                    Pantau status dan progres tiket Anda secara real-time dari mana saja dan kapan saja.
                </p>
            </div>

            <div class="reveal bg-white/95 backdrop-blur rounded-2xl p-8 shadow-md hover:shadow-xl transition border border-white/40 hover:-translate-y-0.5 premium-hover">
                <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mb-6 mx-auto">
                    <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-center text-gray-900 mb-3">Hubungi Kami</h3>
                <p class="text-center text-gray-600">
                    Dapatkan bantuan langsung dari tim support kami melalui berbagai platform komunikasi.
                </p>
            </div>
        </div>
    </div>

    <div class="wave bottom-0 translate-y-full">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
            <path fill="white" d="M0,64 C240,120 480,0 720,56 C960,112 1200,96 1440,32 L1440,120 L0,120 Z"></path>
        </svg>
    </div>
</div>

<!-- TENTANG KAMI -->
<div id="tentang-kami" class="relative bg-white py-16 scroll-mt-24 overflow-hidden section-glow soft-noise">
    <div class="pointer-events-none absolute -top-24 -left-24 h-72 w-72 rounded-full bg-blue-200 blur-3xl opacity-40"></div>
    <div class="pointer-events-none absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-orange-200 blur-3xl opacity-40"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative section-inner">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <div class="reveal">
                <h2 class="mt-5 text-3xl sm:text-4xl font-bold text-gray-900 leading-tight">
                    SIAPKANGMAS,
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-blue-400">
                        lebih dekat
                    </span>
                    & lebih cepat.
                </h2>

                <p class="mt-4 text-gray-600 text-lg leading-relaxed">
                    SIAPKANGMAS adalah layanan bantuan terintegrasi untuk memudahkan masyarakat dan pegawai
                    dalam menyampaikan <span class="font-semibold text-gray-800">konsultasi</span>,
                    <span class="font-semibold text-gray-800">pengaduan</span>, dan
                    <span class="font-semibold text-gray-800">permohonan informasi</span>.
                </p>

                <div class="mt-6 flex flex-wrap gap-2">
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-gray-50 border border-gray-200 text-sm text-gray-700 premium-hover">
                        <span class="h-2 w-2 rounded-full bg-blue-500"></span> Tracking real-time
                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-gray-50 border border-gray-200 text-sm text-gray-700 premium-hover">
                        <span class="h-2 w-2 rounded-full bg-orange-500"></span> Alur simpel
                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-gray-50 border border-gray-200 text-sm text-gray-700 premium-hover">
                        <span class="h-2 w-2 rounded-full bg-green-500"></span> Terintegrasi
                    </span>
                </div>

                <div class="mt-6 grid gap-3">
                    <div class="flex items-start gap-3 p-4 rounded-2xl border border-gray-100 bg-white/70 backdrop-blur shadow-sm premium-hover">
                        <div class="mt-0.5 h-9 w-9 rounded-xl bg-blue-50 flex items-center justify-center border border-blue-100">
                            <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Transparan</p>
                            <p class="text-gray-600">Status tiket bisa dipantau dengan jelas, tanpa tebak-tebakan.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-4 rounded-2xl border border-gray-100 bg-white/70 backdrop-blur shadow-sm premium-hover">
                        <div class="mt-0.5 h-9 w-9 rounded-xl bg-orange-50 flex items-center justify-center border border-orange-100">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Cepat</p>
                            <p class="text-gray-600">Alur pengajuan sederhana, nyaman dipakai dari HP maupun laptop.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3 p-4 rounded-2xl border border-gray-100 bg-white/70 backdrop-blur shadow-sm premium-hover">
                        <div class="mt-0.5 h-9 w-9 rounded-xl bg-green-50 flex items-center justify-center border border-green-100">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">Terintegrasi</p>
                            <p class="text-gray-600">Semua layanan ada dalam satu platform yang rapi dan konsisten.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative reveal">
                <div class="relative rounded-3xl border border-gray-200 bg-white/70 backdrop-blur shadow-xl p-8 premium-hover">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Ringkasnya…</h3>
                            <p class="mt-2 text-gray-600">
                                Ajukan tiket, pantau progres, dan dapatkan update—lebih cepat, lebih rapi.
                            </p>
                        </div>
                        <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-blue-600 to-blue-400 flex items-center justify-center shadow-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="rounded-2xl border border-gray-200 p-5 bg-white shadow-sm premium-hover">
                            <p class="text-sm text-gray-500">Akses</p>
                            <p class="text-lg font-bold text-gray-900 mt-1">Mudah & Ramah Pengguna</p>
                        </div>
                        <div class="rounded-2xl border border-gray-200 p-5 bg-white shadow-sm premium-hover">
                            <p class="text-sm text-gray-500">Tracking</p>
                            <p class="text-lg font-bold text-gray-900 mt-1">Real-time Status Tiket</p>
                        </div>
                        <div class="rounded-2xl border border-gray-200 p-5 bg-white shadow-sm premium-hover">
                            <p class="text-sm text-gray-500">Layanan</p>
                            <p class="text-lg font-bold text-gray-900 mt-1">3 Jenis Pengajuan</p>
                        </div>
                        <div class="rounded-2xl border border-gray-200 p-5 bg-white shadow-sm premium-hover">
                            <p class="text-sm text-gray-500">Update</p>
                            <p class="text-lg font-bold text-gray-900 mt-1">Notifikasi Progres</p>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-between gap-3 rounded-2xl bg-blue-50 border border-blue-100 px-5 py-4 premium-hover">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Butuh cek cepat?</p>
                            <p class="text-sm text-gray-600">Klik tombol “Lacak Tiket Cepat” dan masukkan ID tiket.</p>
                        </div>
                        <button type="button"
                                class="btn-breathe shrink-0 px-4 py-2 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700 transition"
                                onclick="openQuickTrackModal()">
                            Lacak
                        </button>
                    </div>
                </div>

                <div class="absolute -z-10 inset-0 translate-x-2 translate-y-2 rounded-3xl bg-gray-100"></div>
            </div>
        </div>
    </div>
</div>

<!-- KONTAK -->
<div id="kontak" class="relative bg-slate-50 py-16 scroll-mt-24 overflow-hidden section-glow soft-noise">
    <div class="wave -top-20">
        <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
            <path fill="rgb(248 250 252)" d="M0,80 C240,20 480,120 720,64 C960,8 1200,24 1440,88 L1440,0 L0,0 Z"></path>
        </svg>
    </div>

    <div class="pointer-events-none absolute -top-24 -right-24 h-72 w-72 rounded-full bg-blue-200 blur-3xl opacity-30"></div>
    <div class="pointer-events-none absolute -bottom-24 -left-24 h-72 w-72 rounded-full bg-orange-200 blur-3xl opacity-30"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative section-inner">
        <div class="text-center mb-10 reveal">
            <h2 class="mt-4 text-3xl sm:text-4xl font-bold text-gray-900">
                Hubungi Kami
            </h2>
            <p class="mt-2 text-gray-600 max-w-2xl mx-auto">
                Butuh bantuan atau informasi? Kirim pesan atau cek detail kontak di bawah ini.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
            <!-- LEFT -->
            <div class="relative reveal">
                <div class="rounded-3xl border border-gray-200 bg-blue-50/70 backdrop-blur shadow-xl p-8 premium-hover">
                    <h3 class="text-2xl font-bold text-gray-900">
                        Dinas Perindustrian dan Perdagangan<br class="hidden sm:block">
                        Provinsi Jawa Tengah
                    </h3>

                    <div class="mt-8 space-y-6">
                        <div class="flex items-center gap-4">
                            <div class="h-14 w-14 rounded-2xl bg-blue-600 flex items-center justify-center shadow-md shrink-0">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5s-3 1.343-3 3 1.343 3 3 3z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19.5 8c0 7-7.5 13-7.5 13S4.5 15 4.5 8a7.5 7.5 0 1115 0z"/>
                                </svg>
                            </div>
                            <div class="flex flex-col">
                                <p class="text-lg font-bold text-gray-900">Alamat</p>
                                <p class="mt-1 text-gray-700 leading-relaxed">
                                    Jl. Pahlawan No.4, Pleburan, Kec. Semarang Sel., Kota Semarang, Jawa Tengah 50241
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="h-14 w-14 rounded-2xl bg-purple-500 flex items-center justify-center shadow-md shrink-0">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 8l9 6 9-6M4 6h16a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2z"/>
                                </svg>
                            </div>
                            <div class="flex flex-col">
                                <p class="text-lg font-bold text-gray-900">Email</p>
                                <a href="mailto:siapkangmasdisperindag@gmail.com"
                                   class="mt-1 inline-block text-gray-700 hover:text-blue-700 font-semibold transition">
                                    siapkangmasdisperindag@gmail.com
                                </a>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="h-14 w-14 rounded-2xl bg-orange-500 flex items-center justify-center shadow-md shrink-0">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8v4l3 3"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/>
                                </svg>
                            </div>
                            <div class="flex flex-col">
                                <p class="text-lg font-bold text-gray-900">Jam Kerja</p>
                                <p class="mt-1 text-gray-700">
                                    Senin – Kamis atau Jumat <br>
                                    <span class="font-semibold">07.00 – 15.30 WIB atau 07.00 – 13.30 WIB</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/80 border border-blue-100 text-sm text-gray-700 premium-hover">
                            <span class="h-2 w-2 rounded-full bg-green-500"></span> Respons cepat di jam kerja
                        </span>
                        <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/80 border border-blue-100 text-sm text-gray-700 premium-hover">
                            <span class="h-2 w-2 rounded-full bg-blue-600"></span> Layanan terintegrasi
                        </span>
                    </div>
                </div>

                <div class="absolute -z-10 inset-0 translate-x-2 translate-y-2 rounded-3xl bg-gray-100"></div>
            </div>

            <!-- RIGHT (FORM) -->
            <div class="relative reveal">
                <div class="rounded-3xl border border-gray-200 bg-white/70 backdrop-blur shadow-xl p-8 premium-hover">
                    <h3 class="text-2xl font-bold text-gray-900">Kirim Pesan</h3>
                    <p class="mt-2 text-gray-600">
                        Isi form berikut, nanti tim kami akan menindaklanjuti.
                    </p>

                    {{-- ALERT SUCCESS --}}
                    @if(session('success'))
                        <div data-success-alert class="mt-6 mb-2 flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                            <svg class="mt-0.5 h-5 w-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <div>
                                <p class="font-semibold">Berhasil.</p>
                                <p class="text-sm">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    <form class="mt-6 space-y-6" action="{{ route('contact.send') }}" method="POST">
                        @csrf

                        <div>
                            <label class="block text-sm font-semibold text-gray-800">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required value="{{ old('name') }}"
                                   class="mt-2 w-full border-0 border-b border-gray-300 bg-transparent px-0 py-3 focus:ring-0 focus:border-blue-600"
                                   placeholder="Nama lengkap">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-800">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" required value="{{ old('email') }}"
                                   class="mt-2 w-full border-0 border-b border-gray-300 bg-transparent px-0 py-3 focus:ring-0 focus:border-blue-600"
                                   placeholder="nama@email.com">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-800">Judul</label>
                            <input type="text" name="subject" value="{{ old('subject') }}"
                                   class="mt-2 w-full border-0 border-b border-gray-300 bg-transparent px-0 py-3 focus:ring-0 focus:border-blue-600"
                                   placeholder="Judul pesan">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-800">Komentar atau Pesan <span class="text-red-500">*</span></label>
                            <textarea name="message" rows="5" required
                                      class="mt-2 w-full border-0 border-b border-gray-300 bg-transparent px-0 py-3 focus:ring-0 focus:border-blue-600"
                                      placeholder="Tulis pesan kamu...">{{ old('message') }}</textarea>
                        </div>

                        <div class="pt-2">
                            <button
                                type="submit"
                                class="btn-breathe bg-gradient-to-r from-orange-500 to-orange-400 hover:from-orange-600 hover:to-orange-500
                                       text-white px-8 py-3 rounded-xl font-semibold transition shadow-md hover:shadow-lg"
                            >
                                Send Message
                            </button>
                        </div>
                    </form>

                    <p class="mt-4 text-xs text-gray-500">
                        Dengan mengirim pesan, Anda menyetujui data diproses untuk keperluan layanan.
                    </p>
                </div>

                <div class="absolute -z-10 inset-0 translate-x-2 translate-y-2 rounded-3xl bg-gray-100"></div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL -->
<div id="quickTrackModal" class="fixed inset-0 z-[999] hidden">
    <div id="quickTrackBackdrop" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div id="quickTrackDialog" class="w-full max-w-xl modal-enter">
            <div class="bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden premium-hover">
                <div class="px-6 py-5 bg-gradient-to-r from-orange-500 to-orange-400 text-white">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-xl sm:text-2xl font-bold">Lacak Tiket Cepat</h2>
                            <p class="text-white/90 text-sm mt-1">Cek status pengajuan Anda tanpa login.</p>
                        </div>
                        <button type="button"
                                class="btn-breathe shrink-0 rounded-xl bg-white/15 hover:bg-white/25 transition px-3 py-2"
                                aria-label="Tutup"
                                onclick="closeQuickTrackModal()">
                            ✕
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    @if(session('error'))
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('ticket.search') }}" method="GET" class="space-y-3">
                        <label class="block text-sm font-semibold text-gray-700">
                            ID Tiket
                            <span class="text-gray-400 font-normal">(22–23 karakter)</span>
                        </label>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <input
                                id="quickTrackInput"
                                type="text"
                                name="ticket_id"
                                placeholder="Contoh: ABCD1234...."
                                value="{{ request('ticket_id') }}"
                                class="focus-ring flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                                required
                            >
                            <button
                                type="submit"
                                class="btn-breathe bg-gradient-to-r from-orange-500 to-orange-400 hover:from-orange-600 hover:to-orange-500
                                       text-white px-8 py-3 rounded-xl font-semibold transition shadow-md hover:shadow-lg"
                            >
                                Lacak
                            </button>
                        </div>

                        <div class="text-xs text-gray-500 mt-1">
                            Pastikan ID tiket dimasukkan lengkap (tanpa spasi).
                        </div>
                    </form>

                    <div class="mt-6 flex justify-end gap-2">
                        <button type="button"
                                class="btn-breathe px-5 py-2 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition"
                                onclick="closeQuickTrackModal()">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('quickTrackModal');
    const backdrop = document.getElementById('quickTrackBackdrop');
    const openBtn = document.getElementById('openQuickTrack');
    const input = document.getElementById('quickTrackInput');
    const dialog = document.getElementById('quickTrackDialog');

    function openQuickTrackModal() {
        if (!modal || !dialog) return;

        modal.classList.remove('hidden');
        requestAnimationFrame(() => modal.classList.add('is-open'));

        document.body.classList.add('overflow-hidden');

        dialog.classList.remove('modal-leave', 'modal-leave-active');
        dialog.classList.add('modal-enter');

        requestAnimationFrame(() => {
            dialog.classList.add('modal-enter-active');
            dialog.classList.remove('modal-enter');
        });

        setTimeout(() => input?.focus(), 140);
    }

    function closeQuickTrackModal() {
        if (!modal || !dialog) return;

        modal.classList.remove('is-open');

        dialog.classList.remove('modal-enter-active');
        dialog.classList.add('modal-leave');

        requestAnimationFrame(() => {
            dialog.classList.add('modal-leave-active');
            dialog.classList.remove('modal-leave');
        });

        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            openBtn?.focus();
            dialog.classList.remove('modal-leave-active');
        }, 260);
    }

    openBtn?.addEventListener('click', openQuickTrackModal);
    backdrop?.addEventListener('click', closeQuickTrackModal);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            closeQuickTrackModal();
        }
    });

    @if(session('error'))
        openQuickTrackModal();
    @endif

    document.querySelectorAll('input, textarea, select').forEach(el => {
        el.classList.add('focus-ring');
    });

    const surfaces = document.querySelectorAll('.shadow-md, .shadow-xl, .shadow-2xl');
    surfaces.forEach(el => {
        if (!el.classList.contains('premium-hover')) el.classList.add('premium-hover');
    });

    const revealEls = Array.from(document.querySelectorAll('.reveal'));

    revealEls.forEach((el, i) => {
        el.style.transitionDelay = `${Math.min(i * 60, 360)}ms`;
    });

    const io = new IntersectionObserver((entries) => {
        entries.forEach((e) => {
            if (e.isIntersecting) {
                e.target.classList.add('show');
                io.unobserve(e.target);
            }
        });
    }, { threshold: 0.14, rootMargin: "0px 0px -6% 0px" });

    revealEls.forEach(el => io.observe(el));

    const successAlert = document.querySelector('[data-success-alert]');
    if (successAlert) {
        setTimeout(() => {
            successAlert.style.transition = 'opacity .28s cubic-bezier(.16,1,.3,1), transform .28s cubic-bezier(.16,1,.3,1)';
            successAlert.style.opacity = '0';
            successAlert.style.transform = 'translateY(-6px)';
            setTimeout(() => successAlert.remove(), 320);
        }, 45000);
    }
</script>

@endsection