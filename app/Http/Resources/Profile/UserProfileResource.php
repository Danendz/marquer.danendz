<?php

namespace App\Http\Resources\Profile;

use App\Models\Profile\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin UserProfile */
class UserProfileResource extends JsonResource
{
    public function toArray(Request $_request): array
    {
        return [
            'username' => $this->username,
            'status' => $this->status,
            'location' => $this->location,
            'bio' => $this->bio,
            'avatar_url' => $this->avatar_url,
            'cover_url' => $this->cover_url,
            'cover_type' => $this->cover_type,
        ];
    }
}
