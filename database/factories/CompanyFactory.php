<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'address' => fake()->address(),
            'type' => fake()->randomElement(['pemerintah', 'swasta', 'BUMN', 'NGO']),
            'field' => fake()->randomElement(['Software Engineering', 'Data Science', 'UI/UX Design', 'Cybersecurity']),
            'city' => fake()->randomElement(['Jakarta', 'Bandung', 'Yogyakarta', 'Surabaya']),
            'quota' => fake()->numberBetween(3, 10),
            'contact_person' => fake()->name(),
            'contact_phone' => fake()->phoneNumber(),
            'is_active' => true,
            'is_approved' => true,
            'requested_by' => null,
        ];
    }
}
