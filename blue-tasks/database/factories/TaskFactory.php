<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'titulo' => $this->faker->sentence(4),
            'prioridad' => $this->faker->randomElement(['baja', 'media', 'alta']),
            'estado' => $this->faker->randomElement(['pendiente', 'en_progreso', 'finalizada']),
            'project_id' => Project::factory(),
        ];
    }

    public function pendiente(): self
    {
        return $this->state(fn () => ['estado' => 'pendiente']);
    }

    public function enProgreso(): self
    {
        return $this->state(fn () => ['estado' => 'en_progreso']);
    }

    public function finalizada(): self
    {
        return $this->state(fn () => ['estado' => 'finalizada']);
    }
}
