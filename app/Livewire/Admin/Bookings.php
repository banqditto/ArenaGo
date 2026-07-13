<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('View Bookings')]
class Bookings extends Component
{
    use WithPagination;

    /**
     * Render the component view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        $bookings = Booking::with(['user', 'field', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.admin.bookings', [
            'bookings' => $bookings,
        ])->layout('layouts.app');
    }
}
