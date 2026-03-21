<?php

namespace App\Http\Controllers\Private\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\SendMailRequest;
use App\Http\Resources\ApiResponse;
use App\Services\Profile\MailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MailController extends Controller
{
    public function __construct(
        protected MailService $service
    ) {
    }

    public function received(Request $request): JsonResponse
    {
        $items = $this->service->listReceived($request->user()->id);
        return ApiResponse::success($items);
    }

    public function sent(Request $request): JsonResponse
    {
        $items = $this->service->listSent($request->user()->id);
        return ApiResponse::success($items);
    }

    public function send(SendMailRequest $request): JsonResponse
    {
        $item = $this->service->send(
            $request->user()->id,
            $request->validated('recipient_id'),
            $request->validated('template_type'),
            $request->validated('template_key'),
            $request->validated('message'),
        );

        return ApiResponse::success([
            'id' => $item->id,
            'template_type' => $item->template_type,
            'created_at' => $item->created_at,
        ]);
    }
}
