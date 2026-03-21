<?php

namespace App\Http\Controllers\Private\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\RespondFriendRequestRequest;
use App\Http\Requests\Profile\SendFriendRequestRequest;
use App\Http\Resources\ApiResponse;
use App\Services\Profile\FriendshipService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FriendshipController extends Controller
{
    public function __construct(
        protected FriendshipService $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $friends = $this->service->listFriends($request->user()->id);
        return ApiResponse::success($friends);
    }

    public function pendingRequests(Request $request): JsonResponse
    {
        $requests = $this->service->listPendingRequests($request->user()->id);
        return ApiResponse::success($requests);
    }

    public function sendRequest(SendFriendRequestRequest $request): JsonResponse
    {
        $friendship = $this->service->sendRequest(
            $request->user()->id,
            $request->validated('friend_id'),
        );

        return ApiResponse::success(['id' => $friendship->id, 'status' => $friendship->status]);
    }

    public function respondToRequest(RespondFriendRequestRequest $request, int $id): JsonResponse
    {
        $action = $request->validated('action');

        if ($action === 'accept') {
            $friendship = $this->service->acceptRequest($request->user()->id, $id);
            return ApiResponse::success(['id' => $friendship->id, 'status' => $friendship->status]);
        }

        $this->service->rejectRequest($request->user()->id, $id);
        return ApiResponse::success(null, 'Request rejected');
    }

    public function removeFriend(Request $request, int $friendUserId): JsonResponse
    {
        $this->service->removeFriend($request->user()->id, $friendUserId);
        return ApiResponse::success(null, 'Friend removed');
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate(['username' => 'required|string|min:1']);
        $results = $this->service->search($request->user()->id, $request->input('username'));
        return ApiResponse::success($results);
    }

    public function generateInvite(Request $request): JsonResponse
    {
        $code = $this->service->generateInviteCode($request->user()->id);
        return ApiResponse::success(['invite_code' => $code]);
    }

    public function redeemInvite(Request $request): JsonResponse
    {
        $request->validate(['invite_code' => 'required|string']);
        $friendship = $this->service->redeemInviteCode(
            $request->user()->id,
            $request->input('invite_code'),
        );
        return ApiResponse::success(['id' => $friendship->id, 'status' => $friendship->status]);
    }
}
