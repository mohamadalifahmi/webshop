<?php

namespace Database\Factories;

use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Seller>
 */
class SellerFactory extends Factory
{
    protected $model = Seller::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'store_name' => fake()->company().' Store',
            'slug' => Str::slug(fake()->company()).'-'.Str::lower(Str::random(5)),
            'description' => fake()->sentence(),
            'phone' => fake()->phoneNumber(),
            'governorate' => 'Beirut',
            'status' => 'approved',
            'commission_override' => null,
            'balance' => 0,
            'approved_at' => now(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => 'pending',
            'approved_at' => null,
        ]);
    }
}
