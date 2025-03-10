<?php

namespace App\Http\Controllers;

use Carbon\Traits\ToStringFormat;
use Illuminate\Http\Request;
use App\Models\Photo;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Routing\Action;
use Symfony\Component\Process\Process;

class PhotoController extends Controller
{
    public function create()
    {
        return view('upload');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'type' => 'required|in:celular,ultrassom,ressonancia,tomografia,raio_x,mamografia',
            'location' => 'required|in:abdomen,pulmao,cervical,colon,utero,rim,oral,mama,figado,estomago,outras_localizacoes',
        ]);
        if (strstr(php_uname(), "indows")) {
            $python3_path = exec('where python3');
        } else {
            $python3_path = exec('which python3');
        }

        $size = $request->file('image')->getSize();
        $name = $request->file('image')->getClientOriginalName();
        $name = str_replace(' ', '', $name);
        $nickname = $request->input("nickname");
        $path = $request->file('image')->storeAs("public/images/{$nickname}", $name);
        $orgao = $request->location;
        $photo = new Photo();
        $photo->name = $name;
        $photo->size = $size;
        $photo->type = $request->type;
        $photo->location = $path;
        $photo->save();
        $encodePath = base64_encode($path);
        //$encodePath = str_replace(' ', '', $encodePath);

        if ($photo->type == "ultrassom" && $orgao == "mama") {
            $executavel = implode(' ', [$python3_path]); //. $path ];
            //$executavel = implode(' ', ['/usr/bin/ls']);
            $process = new Process([$executavel, 'classificacaoUltrassomMama.py', $path]);
            $process->start(); // Inicia o processo
            while ($process->isSuccessful())
                ;

            $process->wait(); // Aguarda o processo terminar
            $saida = $process->getOutput();
            $erro = $process->getErrorOutput();

            $resultado = $saida;
            //return '<BR><H1>Resultado = ' . $resultado;

            return redirect()->route('classificationUM', [
                'resultado' => $resultado,
                'path' => $encodePath,
            ]);
        } elseif ($photo->type == "mamografia") {
            $executavel = implode(' ', [$python3_path]); //. $path ];
            //$executavel = implode(' ', ['/usr/bin/ls']);
            $process = new Process([$executavel, 'classificacaoMamografia.py', $path]);
            $process->start(); // Inicia o processo
            while ($process->isSuccessful())
                ;

            $process->wait(); // Aguarda o processo terminar
            $saida = $process->getOutput();
            $erro = $process->getErrorOutput();

            $resultado = $saida;
            //return '<BR><H1>Resultado = ' . $resultado;
 

            return redirect()->route('classificationM', [
                'resultado' => $resultado,
                'path' => $encodePath,
            ]); 

        } elseif ($photo->type == "tomografia" && $orgao == "figado") {
            $executavel = implode(' ', [$python3_path]); //. $path ];
            //$executavel = implode(' ', ['/usr/bin/ls']);
            $process = new Process([$executavel, 'classificacaoTomografiaFigado.py', $path]);
            $process->start(); // Inicia o processo
            while ($process->isSuccessful())
                ;

            $process->wait(); // Aguarda o processo terminar
            $saida = $process->getOutput();
            $erro = $process->getErrorOutput();

            $resultado = $saida;
            //return '<BR><H1>Resultado = ' . $resultado;

            return redirect()->route('classificationTF', [
                'resultado' => $resultado,
                'path' => $encodePath,
            ]);
        } elseif ($photo->type == "ultrassom" && $orgao == "figado") {
            $executavel = implode(' ', [$python3_path]); //. $path ];
            //$executavel = implode(' ', ['/usr/bin/ls']);
            $process = new Process([$executavel, 'classificacaoUltrassomFigado.py', $path]);
            $process->start(); // Inicia o processo
            while ($process->isSuccessful())
                ;

            $process->wait(); // Aguarda o processo terminar
            $saida = $process->getOutput();
            $erro = $process->getErrorOutput();

            $resultado = $saida;
            //return '<BR><H1>Resultado = ' . $resultado;

            return redirect()->route('classificationUF', [
                'resultado' => $resultado,
                'path' => $encodePath,
            ]);
        } elseif ($photo->type == "celular" && $orgao == "utero") {
            $executavel = implode(' ', [$python3_path]); //. $path ];
            //$executavel = implode(' ', ['/usr/bin/ls']);
            $process = new Process([$executavel, 'classificacaoCelularUtero.py', $path]);
            $process->start(); // Inicia o processo
            while ($process->isSuccessful())
                ;

            $process->wait(); // Aguarda o processo terminar
            $saida = $process->getOutput();
            $erro = $process->getErrorOutput();

            $resultado = $saida;
            //return '<BR><H1>Resultado = ' . $resultado;

            return redirect()->route('classificationCU', [
                'resultado' => $resultado,
                'path' => $encodePath,
            ]);
        } elseif ($photo->type == "celular" && $orgao == "mama") {
            $executavel = implode(' ', [$python3_path]); //. $path ];
            //$executavel = implode(' ', ['/usr/bin/ls']);
            $process = new Process([$executavel, 'classificacaoCelularMama.py', $path]);
            $process->start(); // Inicia o processo
            while ($process->isSuccessful())
                ;

            $process->wait(); // Aguarda o processo terminar
            $saida = $process->getOutput();
            $erro = $process->getErrorOutput();

            $resultado = $saida;
            //return '<BR><H1>Resultado = ' . $resultado;

            return redirect()->route('classificationCM', [
                'resultado' => $resultado,
                'path' => $encodePath,
            ]);
        } elseif ($photo->type == "celular" && $orgao == "pulmao") {
            $executavel = implode(' ', [$python3_path]); //. $path ];
            //$executavel = implode(' ', ['/usr/bin/ls']);
            $process = new Process([$executavel, 'classificacaoCelularPulmao.py', $path]);
            $process->start(); // Inicia o processo
            while ($process->isSuccessful())
                ;

            $process->wait(); // Aguarda o processo terminar
            $saida = $process->getOutput();
            $erro = $process->getErrorOutput();

            $resultado = $saida;
            //return '<BR><H1>Resultado = ' . $resultado;

            return redirect()->route('classificationCP', [
                'resultado' => $resultado,
                'path' => $encodePath,
            ]);
        } elseif ($photo->type == "celular" && $orgao == "colon") {
            $executavel = implode(' ', [$python3_path]); //. $path ];
            //$executavel = implode(' ', ['/usr/bin/ls']);
            $process = new Process([$executavel, 'classificacaoCelularColon.py', $path]);
            $process->start(); // Inicia o processo
            while ($process->isSuccessful())
                ;

            $process->wait(); // Aguarda o processo terminar
            $saida = $process->getOutput();
            $erro = $process->getErrorOutput();

            $resultado = $saida;
            //return '<BR><H1>Resultado = ' . $resultado;

            return redirect()->route('classificationCC', [
                'resultado' => $resultado,
                'path' => $encodePath,
            ]);
        }  elseif ($photo->type == "celular" && $orgao == "oral") {
            $executavel = implode(' ', [$python3_path]); //. $path ];
            //$executavel = implode(' ', ['/usr/bin/ls']);
            $process = new Process([$executavel, 'classificacaoCelularOral.py', $path]);
            $process->start(); // Inicia o processo
            while ($process->isSuccessful())
                ;

            $process->wait(); // Aguarda o processo terminar
            $saida = $process->getOutput();
            $erro = $process->getErrorOutput();

            $resultado = $saida;
            //return '<BR><H1>Resultado = ' . $resultado;

            return redirect()->route('classificationCO', [
                'resultado' => $resultado,
                'path' => $encodePath,
            ]);
        } elseif ($photo->type == "celular" && $orgao == "cervical") {
            $executavel = implode(' ', [$python3_path]); //. $path ];
            //$executavel = implode(' ', ['/usr/bin/ls']);
            $process = new Process([$executavel, 'classificacaoCelularCervical.py', $path]);
            $process->start(); // Inicia o processo
            while ($process->isSuccessful())
                ;

            $process->wait(); // Aguarda o processo terminar
            $saida = $process->getOutput();
            $erro = $process->getErrorOutput();

            $resultado = $saida;
            //return '<BR><H1>Resultado = ' . $resultado;

            return redirect()->route('classificationCCv', [
                'resultado' => $resultado,
                'path' => $encodePath,
            ]);
        } elseif ($photo->type == "celular" && $orgao == "rim") {
            $executavel = implode(' ', [$python3_path]); //. $path ];
            //$executavel = implode(' ', ['/usr/bin/ls']);
            $process = new Process([$executavel, 'classificacaoCelularRim.py', $path]);
            $process->start(); // Inicia o processo
            while ($process->isSuccessful())
                ;

            $process->wait(); // Aguarda o processo terminar
            $saida = $process->getOutput();
            $erro = $process->getErrorOutput();

            $resultado = $saida;
            //return '<BR><H1>Resultado = ' . $resultado;

            return redirect()->route('classificationCR', [
                'resultado' => $resultado,
                'path' => $encodePath,
            ]);
        }elseif ($photo->type == "tomografia" && $orgao == "rim") {
            $executavel = implode(' ', [$python3_path]); //. $path ];
            //$executavel = implode(' ', ['/usr/bin/ls']);
            $process = new Process([$executavel, 'classificacaoTomografiaRim.py', $path]);
            $process->start(); // Inicia o processo
            while ($process->isSuccessful())
                ;

            $process->wait(); // Aguarda o processo terminar
            $saida = $process->getOutput();
            $erro = $process->getErrorOutput();

            $resultado = $saida;
            //return '<BR><H1>Resultado = ' . $resultado;

            return redirect()->route('classificationTR', [
                'resultado' => $resultado,
                'path' => $encodePath,
            ]);
        }  elseif ($photo->type == "tomografia" && $orgao == "cerebro") {
            $executavel = implode(' ', [$python3_path]); //. $path ];
            //$executavel = implode(' ', ['/usr/bin/ls']);
            $process = new Process([$executavel, 'classificacaoTomografiaCerebro.py', $path]);
            $process->start(); // Inicia o processo
            while ($process->isSuccessful())
                ;

            $process->wait(); // Aguarda o processo terminar
            $saida = $process->getOutput();
            $erro = $process->getErrorOutput();

            $resultado = $saida;
            //return '<BR><H1>Resultado = ' . $resultado;

            return redirect()->route('classificationTC', [
                'resultado' => $resultado,
                'path' => $encodePath,
            ]);
        } elseif ($photo->type == "tomografia" && $orgao == "abdomen") {
            $executavel = implode(' ', [$python3_path]); //. $path ];
            //$executavel = implode(' ', ['/usr/bin/ls']);
            $process = new Process([$executavel, 'classificacaoTomografiaAbdomen.py', $path]);
            $process->start(); // Inicia o processo
            while ($process->isSuccessful())
                ;

            $process->wait(); // Aguarda o processo terminar
            $saida = $process->getOutput();
            $erro = $process->getErrorOutput();

            $resultado = $saida;
            //return '<BR><H1>Resultado = ' . $resultado;

            return redirect()->route('classificationTA', [
                'resultado' => $resultado,
                'path' => $encodePath,
            ]);
        } else {
            return '<BR><H1>Ainda não estamos analisando este tipo e exame.<BR>Em breve estará disponível';
        }
    }
}
