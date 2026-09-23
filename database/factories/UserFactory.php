<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserMenu;
use App\Models\UserPosition;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (User $user) {
            $menus = UserMenu::all()->pluck('id');
            $user->menus()->sync($menus);
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = fake(locale: 'id_ID');
        $position = UserPosition::inRandomOrder()->first();

        return [
            'name' => $faker->name(),
            'username' => $faker->unique()->userName(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'position_id' => $position,
        ];
    }
}
