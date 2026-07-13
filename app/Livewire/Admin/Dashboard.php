<?php

namespace App\Livewire\Admin;

use App\Models\Booking;
use App\Models\Field;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Admin Dashboard')]
class Dashboard extends Component
{
    public int $totalRevenue = 0;
    public int $totalFields = 0;
    public int $totalActiveBookings = 0;

    /**
     * Mount the component and fetch stats.
     */
    public function mount(): void
    {
        $this->totalRevenue = (int) Booking::where('status', 'paid')->sum('total_price');
        $this->totalFields = (int) Field::count();
        $this->totalActiveBookings = (int) Booking::whereIn('status', ['paid', 'pending'])->count();
    }

    /**
     * Render the component view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.admin.dashboard')->layout('layouts.app');
    }
}
