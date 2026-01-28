<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->string('titulo', 150);
            $table->enum('prioridad', ['baja', 'media', 'alta'])->default('media');
            $table->enum('estado', ['pendiente', 'en_progreso', 'finalizada'])->default('pendiente');

            $table->foreignId('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();

            $table->timestamps();

            // Índice CLAVE: soporta el endpoint de filtros combinados + paginación
            $table->index(['project_id', 'estado', 'prioridad']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
