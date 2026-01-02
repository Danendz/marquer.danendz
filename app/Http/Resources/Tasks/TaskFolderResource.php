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
     * Includes `id`, `name`, `created_at`, and `updated_at`; includes `categories` only when the
     * `categories` relationship has been loaded.
     *
     * @return array The resource represented as an associative array.
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
            )
        ];
    }
}