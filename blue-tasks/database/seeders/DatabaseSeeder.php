<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Usuarios fijos (para poder loguearse)
        $owner = User::factory()->create([
            'name' => 'Owner User',
            'email' => 'owner@blue-tasks.test',
            'password' => Hash::make('password'),
        ]);

        $user2 = User::factory()->create([
            'name' => 'Second User',
            'email' => 'user2@blue-tasks.test',
            'password' => Hash::make('password'),
        ]);

        $user3 = User::factory()->create([
            'name' => 'Third User',
            'email' => 'user3@blue-tasks.test',
            'password' => Hash::make('password'),
        ]);

        // 2) Proyectos del owner (con estados variados)
        $projects = Project::factory()
            ->count(3)
            ->for($owner, 'owner')
            ->sequence(
                ['estado' => 'nuevo'],
                ['estado' => 'en_progreso'],
                ['estado' => 'completado'],
            )
            ->create();

        // 3) Tareas por proyecto (mix de estados/prioridades)
        foreach ($projects as $project) {
            $tasks = Task::factory()
                ->count(8)
                ->for($project)
                ->create();

            // 4) Comentarios por tarea (por usuarios random autenticados)
            foreach ($tasks as $task) {
                Comment::factory()
                    ->count(rand(0, 4))
                    ->for($task)
                    ->state(fn () => ['user_id' => collect([$owner->id, $user2->id, $user3->id])->random()])
                    ->create();
            }
        }

        // 5) Proyectos extra de otro usuario (para probar permisos)
        Project::factory()
            ->count(2)
            ->for($user2, 'owner')
            ->create()
            ->each(function (Project $p) {
                Task::factory()->count(5)->for($p)->create();
            });
    }
}
