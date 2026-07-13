<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        <style>
            .glow-green {
                text-shadow: 0 0 12px rgba(34, 197, 94, 0.4);
            }
            .bg-grid-pattern {
                background-size: 40px 40px;
                background-image: linear-gradient(to right, rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                                  linear-gradient(to bottom, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            }
        </style>
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-100 antialiased selection:bg-emerald-500 selection:text-slate-950">
        <!-- Background Grid and Glows -->
        <div class="absolute inset-0 bg-grid-pattern pointer-events-none z-0"></div>
        <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] rounded-full bg-emerald-500/10 blur-[120px] pointer-events-none z-0"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full bg-emerald-500/10 blur-[120px] pointer-events-none z-0"></div>

        <div class="relative z-10 flex flex-col min-h-screen">
            <!-- Navbar -->
            <header class="w-full max-w-7xl mx-auto px-6 py-5 flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-3 font-bold text-xl tracking-tight text-white group">
                    <div class="flex aspect-square size-10 items-center justify-center rounded-lg bg-emerald-500 shadow-md shadow-emerald-500/20 group-hover:scale-105 transition-all duration-300 overflow-hidden">
                        <img src="{{ asset('images/logo-arenago.png') }}" class="size-10 object-cover" alt="Arena Go Logo" />
                    </div>
                    <span class="text-2xl font-black bg-gradient-to-r from-white to-slate-400 bg-clip-text text-transparent">Arena <span class="text-emerald-400 glow-green">Go</span></span>
                </a>

                @if (Route::has('login'))
                    <nav class="flex items-center gap-4">
                        @auth
                            <a
                                href="{{ route('dashboard') }}"
                                class="inline-flex items-center justify-center px-6 py-2.5 rounded-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold transition-all duration-200 shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/30 text-sm"
                            >
                                {{ __('Dashboard') }}
                            </a>
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="inline-flex items-center justify-center px-5 py-2.5 text-slate-300 hover:text-white font-semibold transition-colors duration-200 text-sm"
                            >
                                {{ __('Log in') }}
                            </a>

                            @if (Route::has('register'))
                                <a
                                    href="{{ route('register') }}"
                                    class="inline-flex items-center justify-center px-6 py-2.5 rounded-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold transition-all duration-200 shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/30 text-sm"
                                >
                                    {{ __('Register') }}
                                </a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </header>

            <!-- Hero Section -->
            <main class="flex-1 flex items-center justify-center w-full max-w-7xl mx-auto px-6 py-12 lg:py-20">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center w-full">
                    <!-- Left Hero Content -->
                    <div class="lg:col-span-7 flex flex-col items-start text-left space-y-6">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold uppercase tracking-wider">
                            <span class="flex h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            {{ __('Platform Booking Lapangan No. 1') }}
                        </div>

                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight">
                            Booking Lapangan Olahraga <br class="hidden sm:inline">
                            <span class="bg-gradient-to-r from-emerald-400 via-teal-300 to-emerald-500 bg-clip-text text-transparent glow-green">
                                Jadi Lebih Mudah
                            </span>
                        </h1>

                        <p class="text-base sm:text-lg text-slate-400 max-w-xl leading-relaxed">
                            Cari lapangan favoritmu, pilih jadwal yang sesuai, bayar otomatis secara instan, dan terima konfirmasi lewat WhatsApp. Olahraga tanpa ribet dimulai di sini.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto pt-2">
                            <a
                                href="{{ route('login') }}"
                                class="inline-flex items-center justify-center px-8 py-4 rounded-full bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold transition-all duration-300 shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 text-base"
                            >
                                {{ __('Mulai Booking Sekarang') }}
                                <svg class="size-5 ml-2 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                            <a
                                href="#features"
                                class="inline-flex items-center justify-center px-8 py-4 rounded-full border border-slate-700 hover:border-slate-500 bg-slate-900/50 hover:bg-slate-900 text-slate-300 hover:text-white font-bold transition-all duration-300 text-base"
                            >
                                {{ __('Pelajari Fitur') }}
                            </a>
                        </div>
                    </div>

                    <!-- Right Hero Content (Visual Representation) -->
                    <div class="lg:col-span-5 relative flex items-center justify-center">
                        <div class="relative w-full max-w-[420px] aspect-square rounded-2xl border border-slate-800 bg-slate-900/40 backdrop-blur-xl p-8 shadow-2xl shadow-emerald-950/20 overflow-hidden group">
                            <!-- Sports Field Line Drawing Background -->
                            <div class="absolute inset-0 border-[3px] border-dashed border-emerald-500/10 rounded-2xl m-4 flex items-center justify-center pointer-events-none">
                                <div class="w-full h-0 border-t-[3px] border-dashed border-emerald-500/10"></div>
                                <div class="absolute size-28 rounded-full border-[3px] border-dashed border-emerald-500/10"></div>
                            </div>

                            <!-- Decorative Floating Widgets -->
                            <div class="relative z-10 flex flex-col justify-between h-full space-y-6">
                                <!-- Widget 1: Booking Status Mockup -->
                                <div class="rounded-xl border border-slate-800/80 bg-slate-950/80 p-4 shadow-lg transform group-hover:-translate-y-1 transition-transform duration-300">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="size-9 rounded-lg bg-emerald-500/10 flex items-center justify-center border border-emerald-500/20">
                                                <flux:icon name="calendar" class="size-5 text-emerald-400" />
                                            </div>
                                            <div>
                                                <h4 class="text-xs font-bold text-white">{{ __('Futsal Arena A') }}</h4>
                                                <p class="text-[10px] text-slate-400">{{ __('19:00 - 20:00') }}</p>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            {{ __('Paid') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between text-[10px] text-slate-500 border-t border-slate-800/50 pt-2.5">
                                        <span>{{ __('Tanggal Main') }}</span>
                                        <span class="text-slate-300 font-semibold">{{ __('Hari Ini') }}</span>
                                    </div>
                                </div>

                                <!-- Widget 2: Center Feature Highlight -->
                                <div class="flex flex-col items-center text-center space-y-2 py-4">
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-emerald-500 rounded-full blur-md opacity-25"></div>
                                        <div class="relative size-16 rounded-full bg-emerald-500 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                                            <flux:icon name="bolt" class="size-8 text-slate-950" />
                                        </div>
                                    </div>
                                    <h3 class="text-base font-black text-white">{{ __('Instan & Otomatis') }}</h3>
                                    <p class="text-xs text-slate-400 max-w-[240px]">{{ __('Pembayaran QRIS Midtrans langsung terverifikasi tanpa upload bukti transfer.') }}</p>
                                </div>

                                <!-- Widget 3: Quick Stats -->
                                <div class="rounded-xl border border-slate-800/80 bg-slate-950/80 p-4 shadow-lg transform group-hover:translate-y-1 transition-transform duration-300">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <flux:icon name="star" variant="solid" class="size-4 text-amber-400" />
                                            <span class="text-xs font-black text-white">{{ __('4.9 / 5.0') }}</span>
                                            <span class="text-[10px] text-slate-500">{{ __('(920+ Ulasan)') }}</span>
                                        </div>
                                        <div class="flex -space-x-2">
                                            <div class="size-5 rounded-full bg-slate-700 border border-slate-950 text-[8px] flex items-center justify-center font-bold">JD</div>
                                            <div class="size-5 rounded-full bg-emerald-500 border border-slate-950 text-[8px] text-slate-950 flex items-center justify-center font-bold">AH</div>
                                            <div class="size-5 rounded-full bg-slate-600 border border-slate-950 text-[8px] flex items-center justify-center font-bold">MR</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Features Grid Section -->
            <section id="features" class="border-t border-slate-900 bg-slate-950/50 py-16 lg:py-24">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="text-center max-w-xl mx-auto space-y-3 mb-16">
                        <h2 class="text-3xl font-extrabold text-white">{{ __('Kenapa Memilih Arena Go?') }}</h2>
                        <p class="text-sm text-slate-400">{{ __('Kami menyediakan pengalaman pemesanan lapangan olahraga terbaik, cepat, dan transparan.') }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Feature 1 -->
                        <div class="rounded-2xl border border-slate-900 bg-slate-900/20 p-6 flex flex-col space-y-4 hover:border-slate-800 transition-colors duration-200">
                            <div class="size-12 rounded-xl bg-emerald-500/10 flex items-center justify-center border border-emerald-500/20 text-emerald-400">
                                <flux:icon name="magnifying-glass" class="size-6" />
                            </div>
                            <h3 class="text-lg font-bold text-white">{{ __('Pencarian Lapangan Praktis') }}</h3>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                Jelajahi berbagai pilihan lapangan olahraga di sekitarmu dengan info fasilitas, galeri foto, rating ulasan, dan harga sewa per jam secara transparan.
                            </p>
                        </div>

                        <!-- Feature 2 -->
                        <div class="rounded-2xl border border-slate-900 bg-slate-900/20 p-6 flex flex-col space-y-4 hover:border-slate-800 transition-colors duration-200">
                            <div class="size-12 rounded-xl bg-emerald-500/10 flex items-center justify-center border border-emerald-500/20 text-emerald-400">
                                <flux:icon name="credit-card" class="size-6" />
                            </div>
                            <h3 class="text-lg font-bold text-white">{{ __('Verifikasi Pembayaran Realtime') }}</h3>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                Pembayaran digital QRIS, GoPay, dan Transfer Bank terhubung otomatis via Midtrans. Status langsung terupdate menjadi "Paid" setelah transaksi selesai.
                            </p>
                        </div>

                        <!-- Feature 3 -->
                        <div class="rounded-2xl border border-slate-900 bg-slate-900/20 p-6 flex flex-col space-y-4 hover:border-slate-800 transition-colors duration-200">
                            <div class="size-12 rounded-xl bg-emerald-500/10 flex items-center justify-center border border-emerald-500/20 text-emerald-400">
                                <flux:icon name="chat-bubble-left-right" class="size-6" />
                            </div>
                            <h3 class="text-lg font-bold text-white">{{ __('Notifikasi WhatsApp Instan') }}</h3>
                            <p class="text-sm text-slate-400 leading-relaxed">
                                Kirim rincian jadwal booking secara otomatis ke nomor WhatsApp-mu via API Fonnte begitu pembayaran terkonfirmasi. Tunjukkan chat untuk check-in.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Footer -->
            <footer class="border-t border-slate-900 bg-slate-950 py-8">
                <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-xs text-slate-500">
                        &copy; {{ date('Y') }} Arena Go. All rights reserved.
                    </p>
                    <div class="flex items-center gap-6 text-xs text-slate-500">
                        <a href="#" class="hover:text-slate-300 transition-colors">{{ __('Syarat & Ketentuan') }}</a>
                        <a href="#" class="hover:text-slate-300 transition-colors">{{ __('Kebijakan Privasi') }}</a>
                    </div>
                </div>
            </footer>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
