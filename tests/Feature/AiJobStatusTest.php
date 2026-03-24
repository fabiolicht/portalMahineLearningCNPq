<?php

namespace Tests\Feature;

use App\Models\AiJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiJobStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_job_status_payload(): void
    {
        $job = AiJob::query()->create([
            'request_id' => 'req_test_123',
            'exam_type' => 'mamografia',
            'location' => 'mama',
            'file_path' => 'public/images/nick/exame.png',
            'result_route' => 'classificationM',
            'status' => 'completed',
            'result' => 'Tumor_MalignoQQQ',
            'error_message' => null,
        ]);

        $response = $this->getJson('/ai-jobs/' . $job->request_id);

        $response->assertOk()
            ->assertJson([
                'requestId' => 'req_test_123',
                'status' => 'completed',
                'resultRoute' => 'classificationM',
            ]);
    }
}
