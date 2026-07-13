<?php

namespace App\Livewire\Customer;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Customer Dashboard')]
class Dashboard extends Component
{
    /**
     * Render the customer dashboard view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        $userId = Auth::id();
        $timezone = 'Asia/Jakarta';
        $todayStr = Carbon::today($timezone)->toDateString();
        $nowTimeStr = Carbon::now($timezone)->toTimeString();

        // 1. Stats
        $activeBookingsCount = Booking::where('user_id', $userId)
            ->where('status', 'paid')
            ->where('booking_date', '>=', $todayStr)
            ->count();

        $totalTransactionAmount = Booking::where('user_id', $userId)
            ->where('status', 'paid')
            ->sum('total_price');

        $reviewsCount = Review::where('user_id', $userId)->count();

        // 2. Upcoming Booking (Jadwal Terdekat)
        $upcomingBooking = Booking::with('field')
            ->where('user_id', $userId)
            ->where('status', 'paid')
            ->where(function ($q) use ($todayStr, $nowTimeStr) {
                $q->where('booking_date', '>', $todayStr)
                  ->orWhere(function ($sub) use ($todayStr, $nowTimeStr) {
                      $sub->where('booking_date', $todayStr)
                          ->where('start_time', '>=', $nowTimeStr);
                  });
            })
            ->orderBy('booking_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->first();

        // 3. 3 Most Recent Bookings
        $recentBookings = Booking::with('field')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        return view('livewire.customer.dashboard', [
            'activeBookingsCount' => $activeBookingsCount,
            'totalTransactionAmount' => $totalTransactionAmount,
            'reviewsCount' => $reviewsCount,
            'upcomingBooking' => $upcomingBooking,
            'recentBookings' => $recentBookings,
        ])->layout('layouts.app');
    }
}
