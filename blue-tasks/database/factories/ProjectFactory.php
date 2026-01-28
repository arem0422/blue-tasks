<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'nombre' => $this->faker->sentence(3),
            'estado' => $this->faker->randomElement(['nuevo', 'en_progreso', 'completado']),
            'owner_id' => User::factory(),
        ];
    }

    public function nuevo(): self
    {
        return $this->state(fn () => ['estado' => 'nuevo']);
    }

    public function enProgreso(): self
    {
        return $this->state(fn () => ['estado' => 'en_progreso']);
    }

    public function completado(): self
    {
        return $this->state(fn () => ['estado' => 'completado']);
    }
}
