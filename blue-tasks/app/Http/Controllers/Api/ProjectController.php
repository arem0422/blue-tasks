<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('api')->user();

        $projects = Project::query()
            ->where('owner_id', $user->id)
            ->latest()
            ->paginate(10);

        return response()->json($projects);
    }

    public function store(Request $request)
    {
        $user = auth('api')->user();

        $data = $request->validate([
            'nombre' => ['required', 'string', 'min:3', 'max:120'],
            'estado' => ['nullable', 'in:nuevo,en_progreso,completado'],
        ]);

        $project = Project::create([
            'nombre' => $data['nombre'],
            'estado' => $data['estado'] ?? 'nuevo',
            'owner_id' => $user->id,
        ]);

        return response()->json($project, 201);
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);

        // opcional: tareas (si quieres devolverlas aquí)
        $project->loadCount('tasks');

        return response()->json($project);
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $data = $request->validate([
            'nombre' => ['sometimes', 'required', 'string', 'min:3', 'max:120'],
            'estado' => ['sometimes', 'required', 'in:nuevo,en_progreso,completado'],
        ]);

        // Si por update cambian a completado, aplicamos regla
        if (($data['estado'] ?? null) === 'completado' && $project->estado !== 'completado') {
            return $this->complete($request, $project, $data);
        }

        $project->update($data);

        return response()->json($project);
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return response()->json(['message' => 'Proyecto eliminado']);
    }

    /**
     * PATCH /projects/{project}/complete
     * Regla: al completar proyecto, finalizar todas sus tareas.
     */
    public function complete(Request $request, Project $project, ?array $validatedFromUpdate = null)
    {
        $this->authorize('complete', $project);

        $data = $validatedFromUpdate ?? $request->validate([
            'estado' => ['nullable', 'in:completado'], // opcional por endpoint dedicado
        ]);

        DB::transaction(function () use ($project) {
            $project->update(['estado' => 'completado']);

            $project->tasks()->where('estado', '!=', 'finalizada')
                ->update(['estado' => 'finalizada']);
        });

        $project->loadCount('tasks');

        return response()->json([
            'message' => 'Proyecto completado. Tareas finalizadas automáticamente.',
            'project' => $project->fresh(),
        ]);
    }
}
