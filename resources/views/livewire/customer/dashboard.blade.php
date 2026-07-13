<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-6 dark:border-zinc-800 dark:bg-zinc-900/50">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <flux:heading level="1" size="xl" class="font-extrabold">
                    {{ __('Selamat datang kembali, :name!', ['name' => auth()->user()->name]) }}
                </flux:heading>
                <flux:subheading class="mt-1">
                    {{ __('Kelola booking lapangan dan lihat status transaksi Anda dengan mudah.') }}
                </flux:subheading>
            </div>
            <flux:button 
                href="{{ route('fields.index') }}" 
                variant="primary" 
                icon="calendar-days" 
                class="cursor-pointer font-semibold shadow-sm"
                wire:navigate
            >
                {{ __('Cari Lapangan Olahraga') }}
            </flux:button>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Stat Card 1: Booking Aktif -->
        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <flux:text class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">{{ __('Booking Aktif') }}</flux:text>
                <div class="text-2xl font-black text-zinc-900 dark:text-zinc-50">{{ $activeBookingsCount }}</div>
            </div>
            <div class="size-11 rounded-lg bg-emerald-500/10 flex items-center justify-center border border-emerald-500/20 text-emerald-500 dark:text-emerald-400">
                <flux:icon name="calendar" class="size-6" />
            </div>
        </div>

        <!-- Stat Card 2: Total Transaksi -->
        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <flux:text class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">{{ __('Total Transaksi') }}</flux:text>
                <div class="text-2xl font-black text-zinc-900 dark:text-zinc-50">Rp {{ number_format($totalTransactionAmount, 0, ',', '.') }}</div>
            </div>
            <div class="size-11 rounded-lg bg-emerald-500/10 flex items-center justify-center border border-emerald-500/20 text-emerald-500 dark:text-emerald-400">
                <flux:icon name="credit-card" class="size-6" />
            </div>
        </div>

        <!-- Stat Card 3: Ulasan Diberikan -->
        <div class="rounded-xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900 shadow-sm flex items-center justify-between">
            <div class="space-y-1">
                <flux:text class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">{{ __('Ulasan Anda') }}</flux:text>
                <div class="text-2xl font-black text-zinc-900 dark:text-zinc-50">{{ $reviewsCount }}</div>
            </div>
            <div class="size-11 rounded-lg bg-emerald-500/10 flex items-center justify-center border border-emerald-500/20 text-emerald-500 dark:text-emerald-400">
                <flux:icon name="star" variant="solid" class="size-6" />
            </div>
        </div>
    </div>

    <!-- Jadwal Terdekat & Recent Bookings -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Jadwal Terdekat (Col: 5) -->
        <div class="lg:col-span-5 space-y-3">
            <flux:heading level="2" class="font-bold">{{ __('Jadwal Terdekat') }}</flux:heading>
            
            @if($upcomingBooking)
                <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/[0.02] p-5 dark:border-emerald-500/10 dark:bg-emerald-500/[0.01] shadow-sm relative overflow-hidden group">
                    <!-- Decorative layout lines -->
                    <div class="absolute inset-0 border border-dashed border-emerald-500/5 rounded-xl m-2 pointer-events-none"></div>
                    
                    <div class="relative z-10 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-500 dark:text-emerald-400 border border-emerald-500/20">
                                {{ __('Terkonfirmasi') }}
                            </span>
                            <flux:icon name="bolt" class="size-5 text-emerald-500 animate-pulse" />
                        </div>
                        
                        <div>
                            <flux:heading level="3" class="text-lg font-extrabold text-zinc-900 dark:text-zinc-100">
                                {{ $upcomingBooking->field->name }}
                            </flux:heading>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 capitalize">
                                {{ $upcomingBooking->field->type }}
                            </p>
                        </div>
                        
                        <div class="space-y-2 border-t border-dashed border-zinc-200 dark:border-zinc-800/80 pt-3 text-sm">
                            <div class="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                                <span>{{ __('Tanggal') }}</span>
                                <span class="font-semibold text-zinc-900 dark:text-zinc-200">
                                    {{ \Carbon\Carbon::parse($upcomingBooking->booking_date)->format('d M Y') }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between text-zinc-600 dark:text-zinc-400">
                                <span>{{ __('Waktu') }}</span>
                                <span class="font-semibold text-zinc-900 dark:text-zinc-200">
                                    {{ \Carbon\Carbon::parse($upcomingBooking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($upcomingBooking->end_time)->format('H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="rounded-xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900 shadow-sm text-center flex flex-col items-center justify-center py-10 space-y-4">
                    <div class="size-12 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-500">
                        <flux:icon name="calendar" class="size-6" />
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ __('Belum Ada Jadwal Main') }}</h4>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 max-w-[200px] mx-auto">
                            {{ __('Pesan lapangan sekarang untuk mengamankan slot bermain Anda.') }}
                        </p>
                    </div>
                    <flux:button 
                        href="{{ route('fields.index') }}" 
                        variant="ghost" 
                        size="sm" 
                        class="cursor-pointer"
                        wire:navigate
                    >
                        {{ __('Cari Lapangan') }}
                    </flux:button>
                </div>
            @endif
        </div>

        <!-- Recent Bookings (Col: 7) -->
        <div class="lg:col-span-7 space-y-3">
            <div class="flex items-center justify-between">
                <flux:heading level="2" class="font-bold">{{ __('Pemesanan Terbaru') }}</flux:heading>
                <flux:button 
                    href="{{ route('bookings.history') }}" 
                    variant="ghost" 
                    size="sm" 
                    class="cursor-pointer"
                    wire:navigate
                >
                    {{ __('Semua Riwayat') }}
                </flux:button>
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-zinc-100 dark:border-zinc-800/80 bg-zinc-50/50 dark:bg-zinc-900/50">
                                <th class="p-4 font-bold text-zinc-500 dark:text-zinc-400">{{ __('Lapangan') }}</th>
                                <th class="p-4 font-bold text-zinc-500 dark:text-zinc-400">{{ __('Tanggal') }}</th>
                                <th class="p-4 font-bold text-zinc-500 dark:text-zinc-400">{{ __('Jam') }}</th>
                                <th class="p-4 font-bold text-zinc-500 dark:text-zinc-400 text-right">{{ __('Total') }}</th>
                                <th class="p-4 font-bold text-zinc-500 dark:text-zinc-400 text-center">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/80">
                            @forelse($recentBookings as $booking)
                                <tr>
                                    <td class="p-4 font-semibold text-zinc-900 dark:text-zinc-100">
                                        {{ $booking->field->name }}
                                    </td>
                                    <td class="p-4 text-zinc-600 dark:text-zinc-400">
                                        {{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}
                                    </td>
                                    <td class="p-4 text-zinc-600 dark:text-zinc-400">
                                        {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                    </td>
                                    <td class="p-4 text-right font-bold text-zinc-900 dark:text-zinc-100">
                                        Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($booking->status === 'paid')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-500 dark:text-emerald-400 border border-emerald-500/20">
                                                {{ __('Paid') }}
                                            </span>
                                        @elseif($booking->status === 'pending')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-amber-500/10 text-amber-500 dark:text-amber-400 border border-amber-500/20">
                                                {{ __('Pending') }}
                                            </span>
                                        @elseif($booking->status === 'expired')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-zinc-500/10 text-zinc-500 dark:text-zinc-400 border border-zinc-500/20">
                                                {{ __('Expired') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-rose-500/10 text-rose-500 dark:text-rose-400 border border-rose-500/20">
                                                {{ __('Cancelled') }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-zinc-500 dark:text-zinc-400">
                                        {{ __('Belum ada riwayat booking.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
