<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class MedicalAiClient
{
    public function classify(string $examType, string $location, string $filePath): string
    {
        $baseUrl = rtrim((string) config('services.medical_ai.classification_url'), '/');
        $timeout = (int) config('services.medical_ai.timeout', 600);

        $response = Http::timeout($timeout)->post("{$baseUrl}/v1/classify", [
            'examType' => $examType,
            'location' => $location,
            'filePath' => $filePath,
            'requestId' => uniqid('req_', true),
        ]);

        if (!$response->successful()) {
            throw new RuntimeException('Falha ao chamar servico de classificacao.');
        }

        $payload = $response->json();
        $prediction = $payload['prediction'] ?? null;

        if (!is_string($prediction) || $prediction === '') {
            throw new RuntimeException('Resposta invalida do servico de classificacao.');
        }

        return $prediction;
    }
}
