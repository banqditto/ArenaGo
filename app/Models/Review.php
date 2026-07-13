<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $field_id
 * @property int|null $booking_id
 * @property int $rating
 * @property string $comment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @property-read Field $field
 * @property-read Booking|null $booking
 */
#[Fillable(['user_id', 'field_id', 'booking_id', 'rating', 'comment'])]
class Review extends Model
{

    /**
     * Get the user that wrote the review.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the field that this review is for.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Field, $this>
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    /**
     * Get the booking associated with this review.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Booking, $this>
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
