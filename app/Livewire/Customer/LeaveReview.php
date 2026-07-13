<?php

namespace App\Livewire\Customer;

use App\Models\Booking;
use App\Models\Review;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Flux\Flux;
use Exception;

class LeaveReview extends Component
{
    public ?int $bookingId = null;
    public int $rating = 5;
    public string $comment = '';
    public bool $isOpen = false;

    /**
     * Define validation rules.
     *
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:5|max:1000',
        ];
    }

    /**
     * Open review modal for a specific booking.
     *
     * @param int $bookingId
     */
    #[On('openReviewModal')]
    public function openModal(int $bookingId): void
    {
        $booking = Booking::where('id', $bookingId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$booking || $booking->status !== 'paid') {
            Flux::toast(variant: 'danger', text: __('You can only review paid bookings.'));
            return;
        }

        if ($booking->review) {
            Flux::toast(variant: 'danger', text: __('You have already reviewed this booking.'));
            return;
        }

        $this->bookingId = $bookingId;
        $this->rating = 5;
        $this->comment = '';
        $this->isOpen = true;
    }

    /**
     * Store the submitted review.
     */
    public function submitReview(): void
    {
        $this->validate();

        if (!$this->bookingId) {
            return;
        }

        $booking = Booking::where('id', $this->bookingId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$booking || $booking->status !== 'paid') {
            Flux::toast(variant: 'danger', text: __('Unauthorized review action.'));
            return;
        }

        try {
            Review::create([
                'user_id' => Auth::id(),
                'field_id' => $booking->field_id,
                'booking_id' => $booking->id,
                'rating' => $this->rating,
                'comment' => $this->comment,
            ]);

            Flux::toast(variant: 'success', text: __('Thank you! Your review has been submitted.'));
            
            $this->dispatch('review-submitted');
            $this->isOpen = false;

        } catch (Exception $e) {
            Flux::toast(variant: 'danger', text: __('Failed to submit review: ') . $e->getMessage());
        }
    }

    /**
     * Render the component view.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.customer.leave-review');
    }
}
