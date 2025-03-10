<?php

namespace App\Http\Controllers;

use Carbon\Traits\ToStringFormat;
use Illuminate\Http\Request;
use App\Models\Photo;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Routing\Action;
use Symfony\Component\Process\Process;

class PhotoControllerOLD extends Controller
{
    public function create()
    {
        return view('upload');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'type' => 'required|in:ultrassom,ressonancia,tomografia,raio_x,mamografia',
            'location' => 'required|in:mama,figado,estomago,outras_localizacoes',
        ]);
        
        $size = $request->file('image')->getSize();
        $name = $request->file('image')->getClientOriginalName();
        $nickname = $request->input("nickname");
        $path = $request->file('image')->storeAs("public/images/{$nickname}", $name);

        $photo = new Photo();
        $photo->name = $name;
        $photo->size = $size;
        $photo->type = $request->type;
        $photo->location = $path;
        $photo->save();
        $encodePath = base64_encode($path);

        if( $photo->type == "ultrassom"){
            $executavel = implode(' ', ['/opt/anaconda3/bin/python3']); //. $path ];
            //$executavel = implode(' ', ['/usr/bin/ls']);
            $process = new Process([$executavel, 'classificacao.py', $path]);
            $process->start(); // Inicia o processo
            while($process->isSuccessful());

            $process->wait(); // Aguarda o processo terminar
            $saida = $process->getOutput();
            $erro = $process->getErrorOutput();
            
            $resultado = $saida;
            //return '<BR><H1>Resultado = ' . $resultado;
            
            return redirect()->route('classification', [
                'resultado' => $resultado, 
                'path' => $encodePath, 
            ]);
        }else{
            return '<BR><H1>Estamos atualmente trabalhando somente com Ultrassom<BR>Em breve teremos outros exames';
        }
    }
}
