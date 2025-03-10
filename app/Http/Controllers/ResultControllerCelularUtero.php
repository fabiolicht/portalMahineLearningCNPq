<?php

namespace App\Http\Controllers;

use Carbon\Traits\ToStringFormat;
use Illuminate\Http\Request;
use App\Models\Photo;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Routing\Action;
use Symfony\Component\Process\Process;
use Exception;

class ResultControllerCelularUtero extends Controller
{
    public function __invoke(Request $request, string $resultado, string $path)
    {
        if (strstr(php_uname(), "indows")) {
            $python3_path = exec('where python3');
        } else {
            $python3_path = exec('which python3');
        }
        //echo " <BR>Localização do Python3: $python3_path <BR>";

        $path = base64_decode($path);
        $text4 = "<font color='red'>ATENÇÃO:</font> Este algoritmo possui 90% de probabilidade de acerto!";
        if (strstr($resultado, "DYK")) {
            $executavel = implode(' ', [$python3_path]); //. $path ];

            $process = new Process([$executavel, 'segmentacaoDYKCelularUtero.py', $path]);
            $process->start(); // Inicia o processo
            set_time_limit(600); // Define o tempo limite para 600 segundos (10 minutos)
            while ($process->isRunning()) {
                // Aguarde 1 segundo entre as verificações para evitar sobrecarga
                sleep(1);
            }
            //while ($process->isSuccessful());
                
            //$process->wait(); // Aguarda o processo terminar
            $saida = $process->getOutput();
            $erro = $process->getErrorOutput();
            $imagePath = "../../" . str_replace("public/", "", $path);

            $dados = [
                'imagePath' => $imagePath,
                'resultado' => $resultado, //'Detectado DYK',
            ];
            return view('resultado', $dados);

        } elseif (strstr($resultado, "KOC")) {
            $executavel = implode(' ', [$python3_path]); //. $path ];

            $process = new Process([$executavel, 'segmentacaoKOCCelularUtero.py', $path]);
            $process->start(); // Inicia o processo
            set_time_limit(600); // Define o tempo limite para 600 segundos (10 minutos)
            //while ($process->isSuccessful());
                
            $process->wait(); // Aguarda o processo terminar
            $saida = $process->getOutput();
            $erro = $process->getErrorOutput();
            $imagePath = "../../" . str_replace("public/", "", $path);

            $dados = [
                'imagePath' => $imagePath,
                'resultado' => $resultado, //'Detectado KOC',
            ];
            return view('resultado', $dados);
            //echo "<BR>ERRO: " . $erro;
            //echo "<BR>SAIDA: " . $saida;
        } elseif (strstr($resultado, "MEP")) {
            $executavel = implode(' ', [$python3_path]); //. $path ];

            $process = new Process([$executavel, 'segmentacaoMEPCelularUtero.py', $path]);
            $process->start(); // Inicia o processo
            set_time_limit(600); // Define o tempo limite para 600 segundos (10 minutos)
            //while ($process->isSuccessful());
                
            $process->wait(); // Aguarda o processo terminar
            $saida = $process->getOutput();
            $erro = $process->getErrorOutput();
            $imagePath = "../../" . str_replace("public/", "", $path);

            $dados = [
                'imagePath' => $imagePath,
                'resultado' => $resultado, //'Detectado MEP',
            ];
            return view('resultado', $dados);
        } elseif (strstr($resultado, "PAB")) {
            $executavel = implode(' ', [$python3_path]); //. $path ];

            $process = new Process([$executavel, 'segmentacaoPABCelularUtero.py', $path]);
            $process->start(); // Inicia o processo
            set_time_limit(600); // Define o tempo limite para 600 segundos (10 minutos)
            //while ($process->isSuccessful());
                
            $process->wait(); // Aguarda o processo terminar
            $saida = $process->getOutput();
            $erro = $process->getErrorOutput();
            $imagePath = "../../" . str_replace("public/", "", $path);

            $dados = [
                'imagePath' => $imagePath,
                'resultado' => $resultado, //'Detectado PAB',
            ];
            return view('resultado', $dados);
        } elseif (strstr($resultado, "SFI")) {
            $executavel = implode(' ', [$python3_path]); //. $path ];

            $process = new Process([$executavel, 'segmentacaoSFICelularUtero.py', $path]);
            $process->start(); // Inicia o processo
            set_time_limit(600); // Define o tempo limite para 600 segundos (10 minutos)
            //while ($process->isSuccessful());
                
            $process->wait(); // Aguarda o processo terminar
            $saida = $process->getOutput();
            $erro = $process->getErrorOutput();
            $imagePath = "../../" . str_replace("public/", "", $path);

            $dados = [
                'imagePath' => $imagePath,
                'resultado' => $resultado, //
                'Detectado SFI',
            ];
            return view('resultado', $dados);
        } else {
            return ("<BR><H1>A Imagem Foi Classificada como Normal<BR><h2>Não Foram Localizados Tumores Nesta Imagem<h2><BR><BR>" . $text4);
            //return response()->json("<BR><H1>Normal");
        }

    }

    public function getImage($path)
    {
        $image = imagecreatefrompng($path);

        if (!$image) {
            throw new Exception('Erro ao carregar a imagem.');
        }

        return $image;
    }
}