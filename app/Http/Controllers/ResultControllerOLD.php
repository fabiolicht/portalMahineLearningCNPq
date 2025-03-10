<?php

namespace App\Http\Controllers;

use Carbon\Traits\ToStringFormat;
use Illuminate\Http\Request;
use App\Models\Photo;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Routing\Action;
use Symfony\Component\Process\Process;


class ResultControllerOLD extends Controller
{
    public function __invoke(Request $request, string $resultado, string $path)
    {
        $path = base64_decode($path);
        $text4 = "<BR>ATENÇÃO: Este algoritmo possui 87% de probabilidade de acerto!";
        if (strstr($resultado, "Maligno")) {
            $executavel = implode(' ', ['/opt/anaconda3/bin/python3']); //. $path ];
            
            $process = new Process([$executavel, 'segmentacaoMaligno.py', $path]);
            $process->start(); // Inicia o processo
            while ($process->isSuccessful());
            $process->wait(); // Aguarda o processo terminar
            $saida = $process->getOutput();
            $erro = $process->getErrorOutput();
            $text1 = "Tumor Classificado <BR>Pelo Algoritmo <BR>Como Maligno<BR>";
            $text2 = "<BR>As Anomalias Foram Destacadas na Imagem<BR><BR>";
            $text3 = "<BR>Procure Seu Médico Levando Esta Imagem<BR>";
            $imagePath = str_replace("public/", "", $path);
            $imagePath = "../../" . $imagePath . ".png";
            echo "<img src=" . $imagePath . " alt='Imagem'>";
            echo "<p><h2>". $text1 . "</h2></p>";
            echo "<p><h3>". $text2 . "</h3></p>";
            echo "<p><h3>". $text3 . "</h3></p>";
            echo "<p><h4>". $text4 . "</h4></p>";
            
        } elseif (strstr($resultado, "Benigno")) {
            $executavel = implode(' ', ['/opt/anaconda3/bin/python3']); //. $path ];
            
            $process = new Process([$executavel, 'segmentacaoBenigno.py', $path]);
            $process->start(); // Inicia o processo
            while ($process->isSuccessful());
            $process->wait(); // Aguarda o processo terminar
            $saida = $process->getOutput();
            $erro = $process->getErrorOutput();
            $text1 = "Tumor Classificado <BR>Pelo Algoritmo <BR>Como Benigno<BR>";
            $text2 = "<BR>As Anomalias Foram Destacadas na Imagem<BR><BR>";
            $text3 = "<BR>Procure Seu Médico Levando Esta Imagem<BR>";
            
            $imagePath = str_replace("public/", "", $path);
            $imagePath = "../../" . $imagePath . ".png";
            echo "<img src=" . $imagePath . " alt='Imagem'>";
            echo "<p><h2>". $text1 . "</h2></p>";
            echo "<p><h3>". $text2 . "</h3></p>";
            echo "<p><h3>". $text3 . "</h3></p>";
            echo "<p><h4>". $text4 . "</h4></p>;";
            //echo "<BR>ERRO: " . $erro;
            //echo "<BR>SAIDA: " . $saida;
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