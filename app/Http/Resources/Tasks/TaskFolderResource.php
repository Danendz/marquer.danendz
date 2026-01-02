<?php

namespace App\Http\Resources\Tasks;

use App\Models\Tasks\TaskFolder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin TaskFolder */
class TaskFolderResource extends JsonResource
{
    /**
     * Transform the resource into an array suitable for JSON responses.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request (not used to alter the representation).
     * @return array{id: int|string, name: string|null, created_at: \Illuminate\Support\Carbon|null, updated_at: \Illuminate\Support\Carbon|null, categories: \Illuminate\Http\Resources\Json\AnonymousResourceCollection|null} Array containing the task folder's id, name, timestamps, and a collection of category resources when the `categories` relation is loaded.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'categories' => TaskCategoryResource::collection(
                $this->whenLoaded('categories')
            ),
        ];
    }
}