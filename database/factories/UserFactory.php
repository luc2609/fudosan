<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'username' => $this->faker->name(),
            'first_name' => $this->faker->name(),
            'last_name' => $this->faker->name(),
            'first_name_kata' => $this->faker->name(),
            'last_name_kata' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            'remember_token' => Str::random(10),
            'position' => $this->faker->randomElement([1, 2, 3, 4]),
            'dob' => $this->faker->date($format = 'Y-m-d', $max = 'now'),
            'sex' => random_int(1, 2),
            'phone' => $this->faker->phoneNumber(),
            'postcode' => '0600042',
            'address' => $this->faker->address(),
            'avatar' => 'https://ict-imgs.vgcloud.vn/2020/09/01/19/huong-dan-tao-facebook-avatar.jpg',
            'status' => $this->faker->randomElement([1, 2]),
            'flag' => $this->faker->randomElement([1, 2]),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
