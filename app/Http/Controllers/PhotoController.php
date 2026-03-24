<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessMedicalAiJob;
use App\Models\AiJob;
use Illuminate\Http\Request;
use App\Models\Photo;
use App\Services\Ai\MedicalAiClient;
use Symfony\Component\Process\Process;

class PhotoController extends Controller
{
    private const CLASSIFICATION_MAP = [
        'ultrassom:mama' => ['script' => 'classificacao/classificacaoUltrassomMama.py', 'route' => 'classificationUM'],
        'mamografia:*' => ['script' => 'classificacao/classificacaoMamografia.py', 'route' => 'classificationM'],
        'tomografia:figado' => ['script' => 'classificacao/classificacaoTomografiaFigado.py', 'route' => 'classificationTF'],
        'ultrassom:figado' => ['script' => 'classificacao/classificacaoUltrassomFigado.py', 'route' => 'classificationUF'],
        'celular:utero' => ['script' => 'classificacao/classificacaoCelularUtero.py', 'route' => 'classificationCU'],
        'celular:mama' => ['script' => 'classificacao/classificacaoCelularMama.py', 'route' => 'classificationCM'],
        'celular:pulmao' => ['script' => 'classificacao/classificacaoCelularPulmao.py', 'route' => 'classificationCP'],
        'celular:colon' => ['script' => 'classificacao/classificacaoCelularColon.py', 'route' => 'classificationCC'],
        'celular:oral' => ['script' => 'classificacao/classificacaoCelularOral.py', 'route' => 'classificationCO'],
        'celular:cervical' => ['script' => 'classificacao/classificacaoCelularCervical.py', 'route' => 'classificationCCv'],
        'celular:rim' => ['script' => 'classificacao/classificacaoCelularRim.py', 'route' => 'classificationCR'],
        'tomografia:rim' => ['script' => 'classificacao/classificacaoTomografiaRim.py', 'route' => 'classificationTR'],
        'tomografia:cerebro' => ['script' => 'classificacao/classificacaoTomografiaCerebro.py', 'route' => 'classificationTC'],
        'tomografia:abdomen' => ['script' => 'classificacao/classificacaoTomografiaAbdomen.py', 'route' => 'classificationTA'],
        'fotografia:pele' => ['script' => 'classificacao/classificacaoFotografiaPele.py', 'route' => 'classificationFP'],
    ];

    public function create()
    {
        return view('upload');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'type' => 'required|in:celular,ultrassom,ressonancia,tomografia,raio_x,mamografia,fotografia',
            'location' => 'required|in:abdomen,pulmao,cervical,colon,utero,rim,oral,mama,figado,estomago,pele,outras_localizacoes',
        ]);
        $size = $request->file('image')->getSize();
        $name = $request->file('image')->getClientOriginalName();
        $name = str_replace(' ', '', $name);
        $nickname = $request->input('nickname');
        $path = $request->file('image')->storeAs("public/images/{$nickname}", $name);
        $orgao = $request->location;
        $photo = new Photo();
        $photo->name = $name;
        $photo->size = $size;
        $photo->type = $request->type;
        $photo->location = $path;
        $photo->save();
        $encodePath = base64_encode($path);
        $classificationConfig = $this->resolveClassificationConfig($photo->type, $orgao);

        if (!$classificationConfig) {
            return '<BR><H1>Não estamos fazendo análise neste tipo e exame.<BR>
            Você pode utilizar o mesmo tipo de exame de uma outra região para 
            tentar a análise, entretanto a incerteza aumentará';
        }

        if ((bool) config('services.medical_ai.async_enabled')) {
            $requestId = uniqid('req_', true);
            $aiJob = AiJob::query()->create([
                'request_id' => $requestId,
                'exam_type' => $photo->type,
                'location' => $orgao,
                'file_path' => $path,
                'result_route' => $classificationConfig['route'],
                'status' => 'queued',
            ]);

            ProcessMedicalAiJob::dispatch($aiJob->id, $classificationConfig['script']);

            return response()->json([
                'status' => 'queued',
                'requestId' => $requestId,
                'statusUrl' => route('ai.job.status', ['requestId' => $requestId]),
            ], 202);
        }

        $resultado = $this->resolveClassificationResult($photo->type, $orgao, $path, $classificationConfig['script']);

        return redirect()->route($classificationConfig['route'], [
            'resultado' => $resultado,
            'path' => $encodePath,
        ]);
    }

    private function resolvePythonPath(): string
    {
        return strstr(php_uname(), 'indows')
            ? (exec('where python3') ?: 'python3')
            : (exec('which python3') ?: 'python3');
    }

    private function resolveClassificationConfig(string $type, string $location): ?array
    {
        $specificKey = "{$type}:{$location}";
        $wildcardKey = "{$type}:*";

        return self::CLASSIFICATION_MAP[$specificKey]
            ?? self::CLASSIFICATION_MAP[$wildcardKey]
            ?? null;
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
