<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskCommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $data = $request->validate([
            'cuerpo' => ['required', 'string'],
        ]);

        $user = auth('api')->user();

        $comment = Comment::create([
            'cuerpo' => $data['cuerpo'],
            'task_id' => $task->id,
            'user_id' => $user->id,
        ]);

        // Devuelve el comentario con el autor (útil para Angular)
        return response()->json(
            $comment->load('user'),
            201
        );
    }
}
