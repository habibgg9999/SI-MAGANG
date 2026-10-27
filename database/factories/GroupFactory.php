<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Group>
 */
class GroupFactory extends Factory
{
    protected $model = Group::class;

    public function definition(): array
    {
        return [
            'name' => 'Kelompok ' . fake()->unique()->word(),
            'leader_id' => User::factory(),
            'max_members' => 5,
            'is_open' => true,
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
