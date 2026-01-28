<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'estado' => ['nullable', 'in:pendiente,en_progreso,finalizada'],
            'prioridad' => ['nullable', 'in:baja,media,alta'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $user = auth('api')->user();

        // Solo tareas de proyectos del owner (porque el enunciado lo exige para gestión)
        $tasks = Task::query()
            ->with(['project']) // evita N+1 y ayuda policies
            ->whereHas('project', fn ($q) => $q->where('owner_id', $user->id))
            ->filter($filters)
            ->latest()
            ->paginate($filters['per_page'] ?? 10);

        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo' => ['required', 'string', 'min:3', 'max:150'],
            'prioridad' => ['required', 'in:baja,media,alta'],
            'estado' => ['nullable', 'in:pendiente,en_progreso,finalizada'],
            'project_id' => ['required', 'integer', 'exists:projects,id'],
        ]);

        $project = Project::findOrFail($data['project_id']);

        // Solo owner del proyecto puede crear tareas
        $this->authorize('create', [Task::class, $project]);

        // Si el proyecto está completado, no tiene sentido crear tareas activas (decisión sensata)
        if ($project->estado === 'completado') {
            return response()->json(['message' => 'No puedes crear tareas en un proyecto completado.'], 422);
        }

        $task = Task::create([
            'titulo' => $data['titulo'],
            'prioridad' => $data['prioridad'],
            'estado' => $data['estado'] ?? 'pendiente',
            'project_id' => $project->id,
        ]);

        return response()->json($task->load('project'), 201);
    }

    public function show(Task $task)
    {
        $task->load(['project', 'comments.user']);

        // Para ver detalle, yo lo limitaría a owner del proyecto (coherente con tu index).
        $this->authorize('view', $task);

        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        $task->load('project');
        $this->authorize('update', $task);

        $data = $request->validate([
            'titulo' => ['sometimes', 'required', 'string', 'min:3', 'max:150'],
            'prioridad' => ['sometimes', 'required', 'in:baja,media,alta'],
            'estado' => ['sometimes', 'required', 'in:pendiente,en_progreso,finalizada'],
        ]);

        // Si el proyecto está completado, forzamos consistencia
        if ($task->project->estado === 'completado') {
            return response()->json(['message' => 'No puedes editar tareas de un proyecto completado.'], 422);
        }

        $task->update($data);

        return response()->json($task->fresh()->load('project'));
    }

    public function destroy(Task $task)
    {
        $task->load('project');
        $this->authorize('delete', $task);

        if ($task->project->estado === 'completado') {
            return response()->json(['message' => 'No puedes eliminar tareas de un proyecto completado.'], 422);
        }

        $task->delete();

        return response()->json(['message' => 'Tarea eliminada']);
    }
}
