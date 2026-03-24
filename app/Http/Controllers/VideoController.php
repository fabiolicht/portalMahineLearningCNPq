<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessMedicalAiJob;
use App\Models\AiJob;
use Illuminate\Http\Request;
use App\Models\video;
use App\Services\Ai\MedicalAiClient;
use Symfony\Component\Process\Process;

class VideoController extends Controller
{
    public function create()
    {
        return view('uploadV');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'video' => 'required|mimes:mpeg,mpg,mp4,mkv,webm|max:150000',
            'type' => 'required|in:celular,ultrassom,ressonancia,tomografia,raio_x,mamografia,fotografia',
            'location' => 'required|in:abdomen,pulmao,cervical,colon,utero,rim,oral,mama,figado,estomago,pele,outras_localizacoes',
        ]);
        $size = $request->file('video')->getSize();
        $name = $request->file('video')->getClientOriginalName();
        $name = str_replace(' ', '', $name);
        $nickname = $request->input('nickname');
        $path = $request->file('video')->storeAs("public/videos/{$nickname}", $name);
        $orgao = $request->location;
        $video = new video();
        $video->name = $name;
        $video->size = $size;
        $video->type = $request->type;
        $video->location = $path;
        $video->save();
        $encodePath = base64_encode($path);
        if ($video->type == 'ultrassom' && $orgao == 'mama') {
            if ((bool) config('services.medical_ai.async_enabled')) {
                $requestId = uniqid('req_', true);
                $aiJob = AiJob::query()->create([
                    'request_id' => $requestId,
                    'exam_type' => $video->type,
                    'location' => $orgao,
                    'file_path' => $path,
                    'result_route' => 'classificationUMV',
                    'status' => 'queued',
                ]);

                ProcessMedicalAiJob::dispatch($aiJob->id, 'classificacao/classificacaoUltrassomMamaVideo.py');

                return response()->json([
                    'status' => 'queued',
                    'requestId' => $requestId,
                    'statusUrl' => route('ai.job.status', ['requestId' => $requestId]),
                ], 202);
            }

            $resultado = $this->resolveClassificationResult(
                $video->type,
                $orgao,
                $path,
                'classificacao/classificacaoUltrassomMamaVideo.py'
            );
            return redirect()->route('classificationUMV', [
                'resultado' => $resultado,
                'path' => $encodePath,
            ]);
        } else {
            return '<BR><H1>Não estamos fazendo análise neste tipo e exame.<BR>
            Você pode utilizar o mesmo tipo de exame de uma outra região para 
            tentar a análise, entretanto a incerteza aumentará';
        }
    }

    private function resolvePythonPath(): string
    {
        return strstr(php_uname(), 'indows')
            ? (exec('where python3') ?: 'python3')
            : (exec('which python3') ?: 'python3');
    }

    private function runClassification(string $pythonPath, string $scriptPath, string $path): string
    {
        $process = new Process([$pythonPath, $scriptPath, $path]);
        $process->setTimeout(600);
        $process->run();

        return $process->getOutput();
    }

    private function resolveClassificationResult(string $type, string $location, string $path, string $scriptPath): string
    {
        if ((bool) config('services.medical_ai.enabled')) {
            try {
                /** @var MedicalAiClient $client */
                $client = app(MedicalAiClient::class);
                return $client->classify($type, $location, $path);
            } catch (\Throwable $e) {
                // fallback local para preservar compatibilidade em caso de indisponibilidade
            }
        }

        $pythonPath = $this->resolvePythonPath();
        return $this->runClassification($pythonPath, $scriptPath, $path);
    }
}
