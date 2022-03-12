<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'user_id' => rand(0, 9) > 4 ? User::inRandomOrder()->first()->id : null,
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'reservation_at' => (new Carbon($this->faker->dateTimeBetween('now', '+1 week')))->startOfDay()->addHours(rand(10, 17)),
        ];
    }
}
