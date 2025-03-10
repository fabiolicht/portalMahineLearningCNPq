<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\Process\Process;

class ImageValidationController extends Controller
{
    // Função para validar a imagem
    public function validateImage(Request $request)
    {
        try {
            // Validação inicial da imagem
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            if (strstr(php_uname(), "indows")) {
                $python3_path = exec('where python3');
            } else {
                $python3_path = exec('which python3');
            }

            // Gerar um nome de arquivo único com a extensão original
            $originalExtension = $request->file('image')->getClientOriginalExtension();
            $uniqueFileName = uniqid() . '.' . $originalExtension;

            // Caminho completo para salvar a imagem em 'public/images'
            $destinationPath = 'images/' . $uniqueFileName;
            $request->file('image')->storeAs('public', $destinationPath);

            // Caminho físico para o arquivo salvo
            $imagePath = public_path('storage/' . $destinationPath);
            $type = $request->input('type');

            // Chama o script Python para validação, passando o caminho da imagem
            $executavel = implode(' ', [$python3_path, "validaImage.py", escapeshellarg($imagePath)]);
            $output = shell_exec($executavel);

            // Interpreta a resposta do script Python
            if (trim($output) == 'True') {
                return response()->json([
                    'valid' => true,
                    'output' => $output
                ]);
            } else {
                return response()->json([
                    'valid' => true, #false
                    'output' => $output
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao processar a imagem.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

}
