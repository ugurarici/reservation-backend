<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Append attributes to serialize.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'is_admin',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the reservations for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Reservation>
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Get is_admin attribute.
     *
     * @return bool
     */
    public function getIsAdminAttribute()
    {
        return $this->type === 'admin';
    }

    /**
     * Declare User's accessible Reservations, return QueryBuilder
     *
     * @return \Illuminate\Database\Eloquent\Builder||\Illuminate\Database\Eloquent\Relations\HasMany<Reservation>
     */
    public function accessibleReservations()
    {
        if ($this->is_admin) {
            return Reservation::query();
        } else {
            return $this->reservations();
        }
    }
}
