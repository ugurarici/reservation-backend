<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "user_id",
        "name",
        "email",
        "phone",
        "reservation_at",
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'reservation_at' => 'datetime',
    ];

    public $appends = [
        // 'is_past',
        'is_oncoming',
    ];

    /**
     * Get the user that owns the reservation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get is_past attribute.
     *
     * @return bool
     */
    public function getIsPastAttribute()
    {
        return $this->reservation_at->isPast();
    }

    /**
     * Get is_oncoming attribute.
     *
     * @return bool
     */
    public function getIsOncomingAttribute()
    {
        if ($this->user) {
            $usersClosestReservation = $this->user->reservations()
                ->where('reservation_at', '>=', now())
                ->orderBy('reservation_at', 'asc')
                ->first();
            if (!$usersClosestReservation) return false;
            return $this->id === $usersClosestReservation->id;
        }

        return false;
    }
}
