<?php

namespace App\Livewire\Customer;

use App\Models\Booking;
use App\Models\Field;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Flux\Flux;
use Exception;

/**
 * @property-read array<int, array<string, mixed>> $hourlySlots
 */
#[Title('Field Details')]
class FieldDetail extends Component
{
    public Field $field;
    public string $selectedDate = '';
    /** @var array<int, string> */
    public array $selectedSlots = [];

    /**
     * Mount the component.
     *
     * @param Field $field
     */
    public function mount(Field $field): void
    {
        $this->field = $field;
        $this->selectedDate = Carbon::today('Asia/Jakarta')->format('Y-m-d');
    }

    /**
     * Reset selected slots when selected date changes.
     */
    public function updatedSelectedDate(): void
    {
        $this->selectedSlots = [];
    }

    /**
     * Toggle a slot selection.
     *
     * @param string $start
     */
    public function selectSlot(string $start): void
    {
        $slotInfo = $this->getSlotInfo($start);
        if ($slotInfo['status'] !== 'available') {
            Flux::toast(variant: 'danger', text: __('This slot is not available for booking.'));
            return;
        }

        // If slot is already selected, remove it
        if (in_array($start, $this->selectedSlots)) {
            $tempSlots = array_values(array_diff($this->selectedSlots, [$start]));
            
            if (empty($tempSlots)) {
                $this->selectedSlots = [];
                return;
            }

            // Check if remaining slots are consecutive
            $hours = array_map(fn($s) => (int) substr($s, 0, 2), $tempSlots);
            sort($hours);
            $consecutive = true;
            for ($i = 0; $i < count($hours) - 1; $i++) {
                if ($hours[$i + 1] - $hours[$i] !== 1) {
                    $consecutive = false;
                    break;
                }
            }

            if ($consecutive) {
                sort($tempSlots);
                $this->selectedSlots = $tempSlots;
            } else {
                Flux::toast(variant: 'danger', text: __('You can only deselect slots from the start or end of your selection.'));
            }
            return;
        }

        // If adding a new slot
        $tempSlots = $this->selectedSlots;
        $tempSlots[] = $start;

        $hours = array_map(fn($s) => (int) substr($s, 0, 2), $tempSlots);
        sort($hours);

        // Check consecutive
        for ($i = 0; $i < count($hours) - 1; $i++) {
            if ($hours[$i + 1] - $hours[$i] !== 1) {
                Flux::toast(variant: 'danger', text: __('Selected slots must be consecutive.'));
                return;
            }
        }

        // Check max duration of 5 hours
        if (count($hours) > 5) {
            Flux::toast(variant: 'danger', text: __('Booking duration must be between 1 and 5 hours.'));
            return;
        }

        sort($tempSlots);
        $this->selectedSlots = $tempSlots;
    }

    /**
     * Get computed lists of slots for the selected date.
     *
     * @return array<int, array<string, mixed>>
     */
    #[Computed]
    public function hourlySlots(): array
    {
        $slots = [];
        $startHour = 8;
        $endHour = 22;

        for ($hour = $startHour; $hour < $endHour; $hour++) {
            $start = sprintf('%02d:00', $hour);
            $end = sprintf('%02d:00', $hour + 1);
            $slots[] = $this->getSlotInfo($start, $end);
        }

        return $slots;
    }

    /**
     * Helper to compute single slot status.
     *
     * @param string $start
     * @param string|null $end
     * @return array<string, mixed>
     */
    protected function getSlotInfo(string $start, ?string $end = null): array
    {
        if ($end === null) {
            $hour = (int) substr($start, 0, 2);
            $end = sprintf('%02d:00', $hour + 1);
        }

        $timezone = 'Asia/Jakarta';
        $now = Carbon::now($timezone);
        $bookingDate = Carbon::parse($this->selectedDate, $timezone)->startOfDay();
        $isToday = $bookingDate->isToday();
        $isPastDate = $bookingDate->isPast() && !$isToday;

        // Check if slot start is in the past
        $isPast = false;
        if ($isPastDate) {
            $isPast = true;
        } elseif ($isToday) {
            $slotTime = Carbon::createFromFormat('Y-m-d H:i:s', $this->selectedDate . ' ' . $start . ':00', $timezone);
            if ($slotTime->isPast()) {
                $isPast = true;
            }
        }

        if ($isPast) {
            return [
                'start' => $start,
                'end' => $end,
                'status' => 'past',
            ];
        }

        // Check database for active overlaps (paid or pending & unexpired)
        $overlap = Booking::where('field_id', $this->field->id)
            ->where('booking_date', $this->selectedDate)
            ->where(function ($query) use ($now) {
                $query->where('status', 'paid')
                    ->orWhere(function ($q) use ($now) {
                        $q->where('status', 'pending')
                            ->where('expires_at', '>', $now);
                    });
            })
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->exists();

        return [
            'start' => $start,
            'end' => $end,
            'status' => $overlap ? 'booked' : 'available',
        ];
    }

    /**
     * Handle the booking submission.
     *
     * @return mixed
     */
    public function bookNow(): mixed
    {
        if (!Auth::check()) {
            return $this->redirect(route('login'), navigate: true);
        }

        if (empty($this->selectedDate)) {
            $this->addError('booking', __('Please select a booking date.'));
            return null;
        }

        if (empty($this->selectedSlots)) {
            $this->addError('booking', __('Please select at least one slot.'));
            return null;
        }

        $timezone = 'Asia/Jakarta';
        $date = Carbon::parse($this->selectedDate, $timezone)->startOfDay();
        if ($date->isPast() && !$date->isToday()) {
            $this->addError('booking', __('You cannot book fields for past dates.'));
            return null;
        }

        $hours = array_map(fn($s) => (int) substr($s, 0, 2), $this->selectedSlots);
        sort($hours);

        for ($i = 0; $i < count($hours) - 1; $i++) {
            if ($hours[$i + 1] - $hours[$i] !== 1) {
                $this->addError('booking', __('Selected slots must be consecutive.'));
                return null;
            }
        }

        $duration = count($hours);
        if ($duration > 5) {
            $this->addError('booking', __('Booking duration must be between 1 and 5 hours.'));
            return null;
        }

        $startHour = min($hours);
        $endHour = max($hours) + 1;
        $startTime = Carbon::createFromTime($startHour, 0, 0, $timezone)->format('H:i');
        $endTime = Carbon::createFromTime($endHour, 0, 0, $timezone)->format('H:i');

        try {
            $booking = DB::transaction(function () use ($startTime, $endTime, $timezone) {
                $now = Carbon::now($timezone);
                $overlap = Booking::where('field_id', $this->field->id)
                    ->where('booking_date', $this->selectedDate)
                    ->where(function ($query) use ($now) {
                        $query->where('status', 'paid')
                            ->orWhere(function ($q) use ($now) {
                                $q->where('status', 'pending')
                                    ->where('expires_at', '>', $now);
                            });
                    })
                    ->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime)
                    ->lockForUpdate()
                    ->exists();

                if ($overlap) {
                    throw new Exception(__('This slot is no longer available.'));
                }

                return Booking::create([
                    'user_id' => Auth::id(),
                    'field_id' => $this->field->id,
                    'booking_date' => $this->selectedDate,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'total_price' => count($this->selectedSlots) * $this->field->price_per_hour,
                    'status' => 'pending',
                    'expires_at' => Carbon::now($timezone)->addMinutes(10),
                ]);
            });

            Flux::toast(variant: 'success', text: __('Slot locked successfully. Proceeding to checkout.'));
            
            return $this->redirect(route('checkout', $booking), navigate: true);

        } catch (Exception $e) {
            $this->addError('booking', $e->getMessage());
            $this->selectedSlots = [];
            return null;
        }
    }

    /**
     * Get all reviews for the field, sorted by creation date.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review>
     */
    #[Computed]
    public function reviews(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->field->reviews()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Render the component.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.customer.field-detail', [
            'hourlySlots' => $this->hourlySlots,
        ])->layout('layouts.app');
    }
}
