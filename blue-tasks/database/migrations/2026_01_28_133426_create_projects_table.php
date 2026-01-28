<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 120);
            $table->enum('estado', ['nuevo', 'en_progreso', 'completado'])->default('nuevo');

            $table->foreignId('owner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();

            // Útil para listar proyectos por owner
            $table->index(['owner_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
