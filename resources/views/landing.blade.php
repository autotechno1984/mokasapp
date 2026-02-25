<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>MokasApp - Manajemen Showroom Motor Bekas</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Alpine.js (standalone, tidak konflik dengan @fluxScripts di halaman app) -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            .glass {
                background: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
            }
            .dark .glass {
                background: rgba(0, 0, 0, 0.7);
            }
            .moto-gradient {
                background: linear-gradient(135deg, #111 0%, #333 100%);
            }
            .accent-red {
                color: #e53e3e;
            }
            .bg-accent-red {
                background-color: #e53e3e;
            }
        </style>
    </head>
    <body class="antialiased bg-white text-zinc-900 dark:bg-zinc-950 dark:text-zinc-100 font-sans">

        <!-- Navbar -->
        <nav x-data="{
                open: false,
                navigate(href) {
                    this.open = false;
                    setTimeout(() => { window.location.hash = href; }, 120);
                }
             }" class="fixed top-0 z-50 w-full glass border-b border-zinc-200 dark:border-zinc-800">
            <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center gap-2">
                        <x-app-logo-icon class="h-52 -ml-2 pt-5 w-auto text-accent-red" />
                    </div>

                    <div class="hidden md:flex items-center gap-8 text-sm font-medium">
                        <a href="#fitur" class="hover:text-accent-red transition">Fitur</a>
                        <a href="#solusi" class="hover:text-accent-red transition">Solusi</a>
                        <a href="#harga" class="hover:text-accent-red transition">Harga</a>
                        <a href="#kontak" class="hover:text-accent-red transition">Kontak</a>
                    </div>

                    <div class="flex items-center gap-3">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-sm font-medium hover:text-accent-red transition">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="hidden sm:inline text-sm font-medium hover:text-accent-red transition">Masuk</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-accent-red hover:bg-red-700 transition shadow-sm">
                                        Daftar
                                    </a>
                                @endif
                            @endauth
                        @endif

                        <!-- Hamburger Button (mobile only) -->
                        <button @click="open = !open" class="md:hidden flex items-center justify-center w-9 h-9 rounded-lg text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition" aria-label="Toggle menu">
                            <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            <svg x-show="open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="md:hidden border-t border-zinc-200 dark:border-zinc-800 bg-white/95 dark:bg-zinc-950/95 backdrop-blur-md">
                <div class="max-w-7xl mx-auto px-4 py-4 flex flex-col gap-1">
                    <a href="#fitur" @click.prevent="navigate('fitur')" class="px-3 py-2.5 rounded-lg text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:text-accent-red hover:bg-zinc-50 dark:hover:bg-zinc-900 transition">Fitur</a>
                    <a href="#solusi" @click.prevent="navigate('solusi')" class="px-3 py-2.5 rounded-lg text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:text-accent-red hover:bg-zinc-50 dark:hover:bg-zinc-900 transition">Solusi</a>
                    <a href="#harga" @click.prevent="navigate('harga')" class="px-3 py-2.5 rounded-lg text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:text-accent-red hover:bg-zinc-50 dark:hover:bg-zinc-900 transition">Harga</a>
                    <a href="#kontak" @click.prevent="navigate('kontak')" class="px-3 py-2.5 rounded-lg text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:text-accent-red hover:bg-zinc-50 dark:hover:bg-zinc-900 transition">Kontak</a>
                    @if (Route::has('login'))
                        @guest
                            <div class="mt-2 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                                <a href="{{ route('login') }}" class="block px-3 py-2.5 rounded-lg text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:text-accent-red hover:bg-zinc-50 dark:hover:bg-zinc-900 transition">Masuk</a>
                            </div>
                        @endguest
                    @endif
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
            <div class="absolute inset-0 z-0 opacity-10 dark:opacity-20 pointer-events-none">
                <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <defs>
                        <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                            <path d="M 10 0 L 0 0 0 10" fill="none" stroke="currentColor" stroke-width="0.5"/>
                        </pattern> pattern
                    </defs>
                    <rect width="100" height="100" fill="url(#grid)" />
                </svg>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center lg:text-left grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight">
                            Kelola Showroom <span class="accent-red">Motor Bekas</span> Jadi Lebih Profesional
                        </h1>
                        <p class="mt-6 text-lg text-zinc-600 dark:text-zinc-400 max-w-2xl mx-auto lg:mx-0">
                            MokasApp adalah platform manajemen terpadu untuk showroom motor bekas. Kendalikan inventori, pantau penjualan, dan buat laporan keuangan otomatis dalam satu dashboard.
                        </p>
                        <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-base font-bold rounded-lg text-white bg-accent-red hover:bg-red-700 transition shadow-xl hover:shadow-red-500/20">
                                Mulai Gratis Sekarang
                            </a>
                            <a href="#fitur" class="inline-flex items-center justify-center px-8 py-4 border border-zinc-300 dark:border-zinc-700 text-base font-bold rounded-lg text-zinc-900 dark:text-white bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition">
                                Lihat Demo
                            </a>
                        </div>
                        <div class="mt-8 flex items-center justify-center lg:justify-start gap-4 text-sm text-zinc-500">
                            <span class="flex items-center gap-1">
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                Tanpa Kartu Kredit
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                Setup 5 Menit
                            </span>
                        </div>
                    </div>
                    <div class="relative hidden lg:block">
                        <!-- Decorative glow -->
                        <div class="absolute -inset-4 bg-accent-red opacity-20 blur-3xl rounded-full"></div>

                        <!-- Laptop Mockup Container -->
                        <div class="relative z-10 mx-auto w-full max-w-[540px] transform rotate-1 hover:rotate-0 transition-transform duration-500">
                            <!-- Laptop Screen -->
                            <div class="relative bg-zinc-300 dark:bg-zinc-800 rounded-t-2xl p-2 pb-0 shadow-2xl border-t border-x border-zinc-200 dark:border-zinc-700">
                                <div class="bg-white dark:bg-zinc-950 rounded-t-lg overflow-hidden aspect-[16/10] flex border border-zinc-200 dark:border-zinc-800">
                                    <!-- Mini Sidebar -->
                                    <div class="w-12 bg-zinc-900 flex flex-col items-center py-4 gap-4">
                                        <div class="w-7 h-7 bg-accent-red rounded flex items-center justify-center mb-2">
                                            <x-app-logo-icon class="h-4 w-4 text-white" />
                                        </div>
                                        <div class="w-6 h-1 bg-zinc-700 rounded-full"></div>
                                        <div class="w-6 h-1 bg-zinc-700 rounded-full"></div>
                                        <div class="w-6 h-1 bg-zinc-700 rounded-full"></div>
                                        <div class="w-6 h-1 bg-accent-red/30 rounded-full"></div>
                                    </div>

                                    <!-- Main Screen Content -->
                                    <div class="flex-1 p-4 flex flex-col gap-4 overflow-hidden">
                                        <!-- Top Navigation Mockup -->
                                        <div class="flex justify-between items-center mb-2">
                                            <div class="h-3 w-24 bg-zinc-100 dark:bg-zinc-900 rounded"></div>
                                            <div class="flex gap-2">
                                                <div class="h-5 w-5 bg-zinc-50 dark:bg-zinc-900 rounded-full border border-zinc-100 dark:border-zinc-800"></div>
                                                <div class="h-5 w-5 bg-zinc-50 dark:bg-zinc-900 rounded-full border border-zinc-100 dark:border-zinc-800"></div>
                                            </div>
                                        </div>

                                        <!-- Stats Grid -->
                                        <div class="grid grid-cols-3 gap-3">
                                            <div class="p-2.5 bg-zinc-50 dark:bg-zinc-900 rounded-lg border border-zinc-100 dark:border-zinc-800">
                                                <div class="text-[9px] text-zinc-400 font-bold uppercase mb-1">Sales</div>
                                                <div class="text-xs font-black text-zinc-800 dark:text-zinc-200">+12.5%</div>
                                            </div>
                                            <div class="p-2.5 bg-zinc-50 dark:bg-zinc-900 rounded-lg border border-zinc-100 dark:border-zinc-800">
                                                <div class="text-[9px] text-zinc-400 font-bold uppercase mb-1">Revenue</div>
                                                <div class="text-xs font-black text-zinc-800 dark:text-zinc-200">Rp 2.4B</div>
                                            </div>
                                            <div class="p-2.5 bg-accent-red/5 rounded-lg border border-accent-red/20">
                                                <div class="text-[9px] text-accent-red font-bold uppercase mb-1">Stock</div>
                                                <div class="text-xs font-black text-accent-red">158 Unit</div>
                                            </div>
                                        </div>

                                        <!-- Chart Area -->
                                        <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-3 border border-zinc-100 dark:border-zinc-800 flex-1 flex flex-col">
                                            <div class="flex justify-between items-center mb-3">
                                                <div class="text-[10px] font-bold">Sales Analysis</div>
                                                <div class="text-[8px] text-zinc-400">Feb 2026</div>
                                            </div>
                                            <div class="flex-1 relative">
                                                <svg class="w-full h-full overflow-visible" viewBox="0 0 100 40" preserveAspectRatio="none">
                                                    <path d="M0 35 Q 15 32, 30 36 T 60 15 T 100 5" fill="none" stroke="#e53e3e" stroke-width="2" vector-effect="non-scaling-stroke" />
                                                    <path d="M0 35 Q 15 32, 30 36 T 60 15 T 100 5 V 40 H 0 Z" fill="url(#mockup-chart-grad)" opacity="0.1" />
                                                    <defs>
                                                        <linearGradient id="mockup-chart-grad" x1="0" x2="0" y1="0" y2="1">
                                                            <stop offset="0%" stop-color="#e53e3e" />
                                                            <stop offset="100%" stop-color="#e53e3e" stop-opacity="0" />
                                                        </linearGradient>
                                                    </defs>
                                                </svg>
                                            </div>
                                        </div>

                                        <!-- Inventory List Mockup -->
                                        <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-3 border border-zinc-100 dark:border-zinc-800 h-24 overflow-hidden">
                                            <div class="text-[10px] font-bold mb-2">Recent Inventory</div>
                                            <div class="space-y-2">
                                                <div class="flex items-center justify-between py-1 border-b border-zinc-100 dark:border-zinc-800">
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-4 h-4 bg-zinc-200 dark:bg-zinc-800 rounded"></div>
                                                        <div class="text-[9px] font-medium">Honda Vario 160</div>
                                                    </div>
                                                    <div class="px-1.5 py-0.5 rounded-full bg-green-500/10 text-green-500 text-[7px] font-bold uppercase">Ready</div>
                                                </div>
                                                <div class="flex items-center justify-between py-1 border-b border-zinc-100 dark:border-zinc-800">
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-4 h-4 bg-zinc-200 dark:bg-zinc-800 rounded"></div>
                                                        <div class="text-[9px] font-medium">Yamaha NMAX 2023</div>
                                                    </div>
                                                    <div class="px-1.5 py-0.5 rounded-full bg-yellow-500/10 text-yellow-500 text-[7px] font-bold uppercase">Booked</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Laptop Base -->
                            <div class="h-3 bg-zinc-400 dark:bg-zinc-700 rounded-b-xl shadow-xl border-x border-b border-zinc-500/50"></div>
                            <div class="w-32 h-2 bg-zinc-500 dark:bg-zinc-600 mx-auto rounded-b-xl shadow-inner"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats -->
        <section class="bg-zinc-50 dark:bg-zinc-900 py-12 border-y border-zinc-200 dark:border-zinc-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div>
                        <p class="text-3xl font-bold accent-red">500+</p>
                        <p class="text-sm text-zinc-500 uppercase tracking-widest mt-1">Showroom</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold accent-red">10k+</p>
                        <p class="text-sm text-zinc-500 uppercase tracking-widest mt-1">Unit Terjual</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold accent-red">99%</p>
                        <p class="text-sm text-zinc-500 uppercase tracking-widest mt-1">Kepuasan</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold accent-red">24/7</p>
                        <p class="text-sm text-zinc-500 uppercase tracking-widest mt-1">Support</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Fitur Section -->
        <section id="fitur" class="py-24 bg-white dark:bg-zinc-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-base font-bold text-accent-red uppercase tracking-widest">Fitur Unggulan</h2>
                    <p class="mt-2 text-3xl sm:text-4xl font-extrabold text-zinc-900 dark:text-white">Semua yang Anda Butuhkan</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Fitur 1 -->
                    <div class="p-8 rounded-2xl border border-zinc-200 dark:border-zinc-800 hover:border-accent-red transition group">
                        <div class="w-12 h-12 bg-zinc-100 dark:bg-zinc-900 rounded-lg flex items-center justify-center text-accent-red group-hover:bg-accent-red group-hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <h3 class="mt-6 text-xl font-bold">Manajemen Inventori</h3>
                        <p class="mt-4 text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            Pantau stok motor secara real-time. Kelola detail unit, foto, harga modal, dan harga jual dengan mudah.
                        </p>
                    </div>

                    <!-- Fitur 2 -->
                    <div class="p-8 rounded-2xl border border-zinc-200 dark:border-zinc-800 hover:border-accent-red transition group">
                        <div class="w-12 h-12 bg-zinc-100 dark:bg-zinc-900 rounded-lg flex items-center justify-center text-accent-red group-hover:bg-accent-red group-hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        </div>
                        <h3 class="mt-6 text-xl font-bold">Laporan Penjualan</h3>
                        <p class="mt-4 text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            Analisis performa penjualan harian, mingguan, hingga bulanan. Hitung profit otomatis untuk setiap unit.
                        </p>
                    </div>

                    <!-- Fitur 3 -->
                    <div class="p-8 rounded-2xl border border-zinc-200 dark:border-zinc-800 hover:border-accent-red transition group">
                        <div class="w-12 h-12 bg-zinc-100 dark:bg-zinc-900 rounded-lg flex items-center justify-center text-accent-red group-hover:bg-accent-red group-hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <h3 class="mt-6 text-xl font-bold">Multi-Tenant / Cabang</h3>
                        <p class="mt-4 text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            Kelola banyak cabang showroom dalam satu akun. Delegasikan akses ke staf dengan role yang berbeda.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Solusi Section -->
        <section id="solusi" class="py-24 bg-zinc-50 dark:bg-zinc-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-base font-bold text-accent-red uppercase tracking-widest">Solusi</h2>
                    <p class="mt-2 text-3xl sm:text-4xl font-extrabold text-zinc-900 dark:text-white">Masalah yang Kami Selesaikan</p>
                    <p class="mt-4 text-zinc-500 dark:text-zinc-400 max-w-2xl mx-auto">Kelola showroom motor bekas tidak harus rumit. MokasApp hadir untuk menggantikan cara lama yang membuang waktu.</p>
                </div>

                <div class="grid md:grid-cols-2 gap-8 items-start">
                    <!-- Sebelum -->
                    <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
                        <div class="bg-zinc-100 dark:bg-zinc-800 px-6 py-4 flex items-center gap-3">
                            <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span class="font-bold text-zinc-500 dark:text-zinc-400 text-sm uppercase tracking-widest">Cara Lama</span>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 w-5 h-5 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3 h-3 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                </span>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Stok motor dicatat manual di buku atau Excel, rawan human error</p>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 w-5 h-5 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3 h-3 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                </span>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Hitung profit per unit makan waktu karena biaya perbaikan tersebar di berbagai catatan</p>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 w-5 h-5 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3 h-3 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                </span>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Sulit berbagi info unit ke calon pembeli, harus kirim foto satu per satu lewat WhatsApp</p>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 w-5 h-5 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3 h-3 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                </span>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Pemilik tidak bisa pantau performa cabang lain secara real-time</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sesudah -->
                    <div class="rounded-2xl border border-accent-red/30 overflow-hidden">
                        <div class="bg-accent-red px-6 py-4 flex items-center gap-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="font-bold text-white text-sm uppercase tracking-widest">Dengan MokasApp</span>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 w-5 h-5 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </span>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Inventori motor tercatat digital lengkap dengan foto, spesifikasi, dan status per gudang</p>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 w-5 h-5 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </span>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Profit per unit terhitung otomatis — harga beli, biaya perbaikan, hingga harga jual</p>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 w-5 h-5 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </span>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Bagikan detail unit ke calon pembeli cukup lewat satu link — foto, spek, dan harga langsung tampil</p>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 w-5 h-5 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </span>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Pantau semua cabang dari satu dashboard dengan role akses yang bisa dikustomisasi per staf</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Harga Section -->
        <section id="harga" class="py-24 bg-white dark:bg-zinc-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-base font-bold text-accent-red uppercase tracking-widest">Harga</h2>
                    <p class="mt-2 text-3xl sm:text-4xl font-extrabold text-zinc-900 dark:text-white">Pilih Plan yang Tepat</p>
                    <p class="mt-4 text-zinc-500 dark:text-zinc-400 max-w-xl mx-auto">Mulai dari showroom kecil hingga jaringan besar. Semua plan bisa dicoba gratis 14 hari, tanpa kartu kredit.</p>
                </div>

                <div x-data="{ tahunan: false }" class="flex flex-col items-center">
                    <!-- Toggle Bulanan / Tahunan -->
                    <div class="inline-flex items-center mb-12 bg-zinc-100 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-full p-1.5">
                        <button @click="tahunan = false"
                            :class="!tahunan ? 'bg-white dark:bg-zinc-800 shadow text-zinc-900 dark:text-white' : 'text-zinc-400 hover:text-zinc-600'"
                            class="px-5 py-2 rounded-full text-sm font-bold transition">
                            Bulanan
                        </button>
                        <button @click="tahunan = true"
                            :class="tahunan ? 'bg-white dark:bg-zinc-800 shadow text-zinc-900 dark:text-white' : 'text-zinc-400 hover:text-zinc-600'"
                            class="px-5 py-2 rounded-full text-sm font-bold transition flex items-center gap-2">
                            Tahunan
                            <span class="bg-green-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">Hemat 17%</span>
                        </button>
                    </div>

                    <!-- Pricing Cards -->
                    <div class="grid md:grid-cols-3 gap-8 w-full items-stretch">

                        <!-- Starter -->
                        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 p-8 flex flex-col">
                            <div>
                                <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest">Starter</p>
                                <div class="mt-4 flex items-end gap-1">
                                    <span class="text-4xl font-extrabold text-zinc-900 dark:text-white"
                                        x-text="tahunan ? 'Rp 291rb' : 'Rp 349rb'"></span>
                                    <span class="text-zinc-400 text-sm mb-1.5">/bln</span>
                                </div>
                                <p class="text-xs text-zinc-400 mt-1"
                                    x-text="tahunan ? 'Rp 3.490.000 ditagih per tahun' : 'Rp 349.000 ditagih per bulan'"></p>
                                <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">Untuk showroom kecil yang baru mulai digital.</p>
                                <div class="mt-3 flex gap-3 text-xs font-medium text-zinc-400">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        2 user
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        1 cabang
                                    </span>
                                </div>
                            </div>
                            <ul class="mt-8 space-y-3 flex-1 border-t border-zinc-100 dark:border-zinc-800 pt-6">
                                @foreach(['Manajemen Produk', 'Kasir / POS', 'Laporan Penjualan Dasar', 'Manajemen Stok'] as $fitur)
                                <li class="flex items-center gap-2.5 text-sm text-zinc-600 dark:text-zinc-400">
                                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    {{ $fitur }}
                                </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('register') }}" class="mt-8 block text-center py-3 px-6 border border-zinc-300 dark:border-zinc-700 text-sm font-bold rounded-lg text-zinc-900 dark:text-white hover:border-accent-red hover:text-accent-red transition">
                                Mulai Gratis 14 Hari
                            </a>
                        </div>

                        <!-- Professional (Highlighted) -->
                        <div class="rounded-2xl border-2 border-accent-red bg-white dark:bg-zinc-950 p-8 flex flex-col relative shadow-2xl shadow-red-500/10 scale-[1.02]">
                            <div class="absolute -top-4 inset-x-0 flex justify-center">
                                <span class="bg-accent-red text-white text-xs font-bold px-4 py-1.5 rounded-full shadow">Paling Populer</span>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-accent-red uppercase tracking-widest">Professional</p>
                                <div class="mt-4 flex items-end gap-1">
                                    <span class="text-4xl font-extrabold text-zinc-900 dark:text-white"
                                        x-text="tahunan ? 'Rp 499rb' : 'Rp 599rb'"></span>
                                    <span class="text-zinc-400 text-sm mb-1.5">/bln</span>
                                </div>
                                <p class="text-xs text-zinc-400 mt-1"
                                    x-text="tahunan ? 'Rp 5.990.000 ditagih per tahun' : 'Rp 599.000 ditagih per bulan'"></p>
                                <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">Untuk showroom berkembang dengan beberapa cabang.</p>
                                <div class="mt-3 flex gap-3 text-xs font-medium text-zinc-400">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        5 user
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        3 cabang
                                    </span>
                                </div>
                            </div>
                            <ul class="mt-8 space-y-3 flex-1 border-t border-zinc-100 dark:border-zinc-800 pt-6">
                                @foreach(['Manajemen Produk', 'Kasir / POS', 'Laporan Penjualan Lengkap', 'Manajemen Stok', 'Multi Cabang', 'Manajemen Karyawan', 'Promo & Diskon'] as $fitur)
                                <li class="flex items-center gap-2.5 text-sm text-zinc-600 dark:text-zinc-400">
                                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    {{ $fitur }}
                                </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('register') }}" class="mt-8 block text-center py-3 px-6 bg-accent-red text-white text-sm font-bold rounded-lg hover:bg-red-700 transition shadow-lg">
                                Mulai Gratis 14 Hari
                            </a>
                        </div>

                        <!-- Enterprise -->
                        <div class="rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 p-8 flex flex-col">
                            <div>
                                <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest">Enterprise</p>
                                <div class="mt-4 flex items-end gap-1">
                                    <span class="text-4xl font-extrabold text-zinc-900 dark:text-white"
                                        x-text="tahunan ? 'Rp 832rb' : 'Rp 999rb'"></span>
                                    <span class="text-zinc-400 text-sm mb-1.5">/bln</span>
                                </div>
                                <p class="text-xs text-zinc-400 mt-1"
                                    x-text="tahunan ? 'Rp 9.990.000 ditagih per tahun' : 'Rp 999.000 ditagih per bulan'"></p>
                                <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">Untuk jaringan showroom besar & korporasi.</p>
                                <div class="mt-3 flex gap-3 text-xs font-medium text-zinc-400">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        20 user
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        10 cabang
                                    </span>
                                </div>
                            </div>
                            <ul class="mt-8 space-y-3 flex-1 border-t border-zinc-100 dark:border-zinc-800 pt-6">
                                <li class="flex items-center gap-2.5 text-sm text-zinc-600 dark:text-zinc-400">
                                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    Semua fitur Professional
                                </li>
                                @foreach(['API Integrasi', 'Dukungan Prioritas', 'Custom Branding'] as $fitur)
                                <li class="flex items-center gap-2.5 text-sm text-zinc-600 dark:text-zinc-400">
                                    <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    {{ $fitur }}
                                </li>
                                @endforeach
                            </ul>
                            <a href="#kontak" class="mt-8 block text-center py-3 px-6 border border-zinc-300 dark:border-zinc-700 text-sm font-bold rounded-lg text-zinc-900 dark:text-white hover:border-accent-red hover:text-accent-red transition">
                                Hubungi Sales
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        <!-- Kontak Section -->
        <section id="kontak" class="py-24 bg-white dark:bg-zinc-950">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-base font-bold text-accent-red uppercase tracking-widest">Kontak</h2>
                    <p class="mt-2 text-3xl sm:text-4xl font-extrabold text-zinc-900 dark:text-white">Ada Pertanyaan? Hubungi Kami</p>
                    <p class="mt-4 text-zinc-500 dark:text-zinc-400 max-w-xl mx-auto">Tim kami siap membantu Anda memulai dan menjawab semua pertanyaan seputar MokasApp.</p>
                </div>

                <div class="grid md:grid-cols-2 gap-12 items-start">
                    <!-- Info Kontak -->
                    <div class="space-y-8">
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 bg-zinc-100 dark:bg-zinc-900 rounded-lg flex items-center justify-center flex-shrink-0 text-accent-red">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <p class="font-bold text-zinc-900 dark:text-white">Email</p>
                                <p class="text-sm text-zinc-500 mt-1">Kami membalas dalam 1x24 jam kerja</p>
                                <a href="mailto:halo@mokasapp.id" class="text-sm text-accent-red font-medium mt-1 inline-block hover:underline">halo@mokasapp.id</a>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 bg-zinc-100 dark:bg-zinc-900 rounded-lg flex items-center justify-center flex-shrink-0 text-accent-red">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <p class="font-bold text-zinc-900 dark:text-white">WhatsApp</p>
                                <p class="text-sm text-zinc-500 mt-1">Konsultasi langsung dengan tim sales kami</p>
                                <a href="https://wa.me/6281234567890" target="_blank" class="text-sm text-accent-red font-medium mt-1 inline-block hover:underline">+62 812-3456-7890</a>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 bg-zinc-100 dark:bg-zinc-900 rounded-lg flex items-center justify-center flex-shrink-0 text-accent-red">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="font-bold text-zinc-900 dark:text-white">Jam Operasional</p>
                                <p class="text-sm text-zinc-500 mt-1">Senin – Jumat: 08.00 – 17.00 WIB</p>
                                <p class="text-sm text-zinc-500">Sabtu: 08.00 – 13.00 WIB</p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Kontak -->
                    <form class="space-y-5 bg-zinc-50 dark:bg-zinc-900 p-8 rounded-2xl border border-zinc-200 dark:border-zinc-800">
                        <div class="grid sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Nama</label>
                                <input type="text" placeholder="Nama lengkap" class="w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-accent-red/50 focus:border-accent-red transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Email</label>
                                <input type="email" placeholder="email@anda.com" class="w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-accent-red/50 focus:border-accent-red transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Nama Showroom</label>
                            <input type="text" placeholder="Showroom Motor Anda" class="w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-accent-red/50 focus:border-accent-red transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">Pesan</label>
                            <textarea rows="4" placeholder="Ceritakan kebutuhan Anda..." class="w-full px-4 py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-accent-red/50 focus:border-accent-red transition resize-none"></textarea>
                        </div>
                        <button type="submit" class="w-full py-3 px-6 bg-accent-red text-white font-bold rounded-lg hover:bg-red-700 transition shadow-sm">
                            Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="moto-gradient rounded-3xl p-8 md:p-16 text-center text-white relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
                         <img src="https://images.unsplash.com/photo-1591637333184-19aa84b3e01f?auto=format&fit=crop&q=80&w=1200" alt="Motor detail" class="w-full h-full object-cover" />
                    </div>
                    <div class="relative z-10">
                        <h2 class="text-3xl md:text-5xl font-extrabold mb-6">Siap Mengembangkan Showroom Anda?</h2>
                        <p class="text-lg md:text-xl text-zinc-300 mb-10 max-w-2xl mx-auto">
                            Bergabunglah dengan ratusan showroom lainnya yang telah bertransformasi ke digital bersama MokasApp.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="{{ route('register') }}" class="px-8 py-4 bg-white text-zinc-900 font-bold rounded-lg hover:bg-zinc-100 transition shadow-lg">
                                Daftar Gratis 14 Hari
                            </a>
                            <a href="#kontak" class="px-8 py-4 border border-zinc-500 text-white font-bold rounded-lg hover:bg-white/10 transition">
                                Hubungi Sales
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-zinc-50 dark:bg-zinc-900 pt-16 pb-8 border-t border-zinc-200 dark:border-zinc-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid md:grid-cols-4 gap-12 mb-12">
                    <div class="col-span-2 md:col-span-1">
                        <div class="flex items-center gap-2 mb-6">
                            <x-app-logo-icon class="h-6 w-auto text-accent-red" />
                            <span class="text-lg font-bold tracking-tight accent-red">MOKAS<span class="text-zinc-900 dark:text-white">APP</span></span>
                        </div>
                        <p class="text-zinc-500 text-sm leading-relaxed">
                            Solusi manajemen digital terbaik untuk pengusaha motor bekas di Indonesia.
                        </p>
                    </div>
                    <div>
                        <h4 class="font-bold mb-6">Produk</h4>
                        <ul class="space-y-4 text-sm text-zinc-500">
                            <li><a href="#" class="hover:text-accent-red transition">Fitur</a></li>
                            <li><a href="#harga" class="hover:text-accent-red transition">Harga</a></li>
                            <li><a href="#" class="hover:text-accent-red transition">Testimoni</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold mb-6">Perusahaan</h4>
                        <ul class="space-y-4 text-sm text-zinc-500">
                            <li><a href="#" class="hover:text-accent-red transition">Tentang Kami</a></li>
                            <li><a href="#" class="hover:text-accent-red transition">Blog</a></li>
                            <li><a href="#" class="hover:text-accent-red transition">Karir</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold mb-6">Bantuan</h4>
                        <ul class="space-y-4 text-sm text-zinc-500">
                            <li><a href="#" class="hover:text-accent-red transition">Pusat Bantuan</a></li>
                            <li><a href="#" class="hover:text-accent-red transition">Kontak Kami</a></li>
                            <li><a href="#" class="hover:text-accent-red transition">Syarat & Ketentuan</a></li>
                        </ul>
                    </div>
                </div>
                <div class="pt-8 border-t border-zinc-200 dark:border-zinc-800 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p class="text-xs text-zinc-400">
                        &copy; {{ date('Y') }} MokasApp. All rights reserved.
                    </p>
                    <div class="flex gap-6">
                        <!-- Social Media Icons (Placeholders) -->
                        <a href="#" class="text-zinc-400 hover:text-accent-red transition"><span class="sr-only">Facebook</span>FB</a>
                        <a href="#" class="text-zinc-400 hover:text-accent-red transition"><span class="sr-only">Instagram</span>IG</a>
                        <a href="#" class="text-zinc-400 hover:text-accent-red transition"><span class="sr-only">Twitter</span>TW</a>
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>
