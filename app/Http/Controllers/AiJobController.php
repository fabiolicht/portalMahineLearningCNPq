<?php

namespace App\Http\Controllers;

use App\Models\AiJob;
use Illuminate\Http\JsonResponse;

class AiJobController extends Controller
{
    public function show(string $requestId): JsonResponse
    {
        $job = AiJob::query()->where('request_id', $requestId)->firstOrFail();

        return response()->json([
            'requestId' => $job->request_id,
            'status' => $job->status,
            'result' => $job->result,
            'resultRoute' => $job->result_route,
            'error' => $job->error_message,
            'path' => base64_encode($job->file_path),
        ]);
    }
}
