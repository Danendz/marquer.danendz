<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResponseResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
            'user' => new UserResource($this['user']),
            'token' => $this['token'],
            'token_type' => 'bearer',
		];
	}
}
