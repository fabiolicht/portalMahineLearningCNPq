<?php

namespace Tests\Unit\Services\Ai;

use App\Services\Ai\MedicalAiClient;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class MedicalAiClientTest extends TestCase
{
    public function test_it_returns_prediction_from_classification_service(): void
    {
        Config::set('services.medical_ai.classification_url', 'http://classification-service:8001');
        Config::set('services.medical_ai.timeout', 10);

        Http::fake([
            'http://classification-service:8001/v1/classify' => Http::response([
                'status' => 'ok',
                'prediction' => 'Tumor_MalignoQQQ',
            ], 200),
        ]);

        $client = new MedicalAiClient();
        $result = $client->classify('mamografia', 'mama', 'public/images/nick/exame.png');

        $this->assertSame('Tumor_MalignoQQQ', $result);
    }

    public function test_it_throws_exception_for_invalid_service_response(): void
    {
        $this->expectException(RuntimeException::class);

        Config::set('services.medical_ai.classification_url', 'http://classification-service:8001');
        Config::set('services.medical_ai.timeout', 10);

        Http::fake([
            'http://classification-service:8001/v1/classify' => Http::response([
                'status' => 'ok',
            ], 200),
        ]);

        $client = new MedicalAiClient();
        $client->classify('mamografia', 'mama', 'public/images/nick/exame.png');
    }
}
