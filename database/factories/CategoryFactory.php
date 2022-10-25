<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;

class CategoryFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word,
            'slug' => $this->faker->unique()->slug
        ];
    }
}
