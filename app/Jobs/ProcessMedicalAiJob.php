<?php

namespace App\Jobs;

use App\Models\AiJob;
use App\Services\Ai\MedicalAiClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;

class ProcessMedicalAiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $aiJobId,
        public string $scriptPath
    ) {
    }

    public function handle(MedicalAiClient $client): void
    {
        $aiJob = AiJob::query()->find($this->aiJobId);
        if (!$aiJob) {
            return;
        }

        $aiJob->status = 'processing';
        $aiJob->save();

        try {
            if ((bool) config('services.medical_ai.enabled')) {
                $result = $client->classify($aiJob->exam_type, $aiJob->location, $aiJob->file_path);
            } else {
                $pythonPath = strstr(php_uname(), 'indows')
                    ? (exec('where python3') ?: 'python3')
                    : (exec('which python3') ?: 'python3');

                $process = new Process([$pythonPath, $this->scriptPath, $aiJob->file_path]);
                $process->setTimeout(600);
                $process->run();
                $result = $process->getOutput();
            }

            $aiJob->status = 'completed';
            $aiJob->result = $result;
            $aiJob->error_message = null;
            $aiJob->save();
        } catch (\Throwable $e) {
            $aiJob->status = 'failed';
            $aiJob->error_message = $e->getMessage();
            $aiJob->save();
        }
    }
}
