<?php

namespace App\Http\Controllers\Private\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Resources\Tasks\TaskResource;
use App\Models\Tasks\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        return TaskResource::collection(Task::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'task_category_id' => ['required', 'exists:task_categories'],
        ]);

        return new TaskResource(Task::create($data));
    }

    public function show(Task $task)
    {
        return new TaskResource($task);
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'task_category_id' => ['required', 'exists:task_categories'],
        ]);

        $task->update($data);

        return new TaskResource($task);
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json();
    }
}
