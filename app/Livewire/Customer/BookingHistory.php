<?php

namespace App\Livewire\Customer;

use App\Models\Booking;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

#[Title('My Bookings')]
class BookingHistory extends Component
{
    use WithPagination;

    /**
     * Listener to refresh the bookings list on review submission.
     */
    #[On('review-submitted')]
    public function refreshBookings(): void
    {
        // Reactively triggers re-render
    }

    /**
     * Render the booking history page.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with(['field', 'payment', 'review'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.customer.booking-history', [
            'bookings' => $bookings,
        ])->layout('layouts.app');
    }
}
