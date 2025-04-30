<?php

namespace App\Http\Controllers;

use Carbon\Traits\ToStringFormat;
use Illuminate\Http\Request;
use App\Models\video;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Routing\Action;
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
        if (strstr(php_uname(), "indows")) {
            $python3_path = exec('where python3');
        } else {
            $python3_path = exec('which python3');
        }

        $size = $request->file('video')->getSize();
        $name = $request->file('video')->getClientOriginalName();
        $name = str_replace(' ', '', $name);
        $nickname = $request->input("nickname");
        $path = $request->file('video')->storeAs("public/videos/{$nickname}", $name);
        $orgao = $request->location;
        $video = new video();
        $video->name = $name;
        $video->size = $size;
        $video->type = $request->type;
        $video->location = $path;
        $video->save();
        $encodePath = base64_encode($path);
        //$encodePath = str_replace(' ', '', $encodePath);

        if ($video->type == "ultrassom" && $orgao == "mama") {
            $executavel = implode(' ', [$python3_path]); //. $path ];
            //$executavel = implode(' ', ['/usr/bin/ls']);
            $process = new Process([$executavel, 'classificacao/classificacaoUltrassomMamaVideo.py', $path]);
            $process->start(); // Inicia o processo
            while ($process->isSuccessful())
                ;

            $process->wait(); // Aguarda o processo terminar
            $saida = $process->getOutput();
            $erro = $process->getErrorOutput();

            $resultado = $saida;
            //return '<BR><H1>Resultado = ' . $resultado;

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
}
