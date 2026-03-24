@php
$imagePath = $imagePath;
$resultado = $resultado;
$resultado = str_replace("QQQ", "<br>", $resultado);
$resultado = str_replace("__", "%", $resultado);
$resultado = str_replace("_", " ", $resultado);
@endphp

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" type="text/css" href="/css/main.css">
    <title>Resultado da Análise</title>
    <style>
        .result-page {
            max-width: 1200px;
            margin: 32px auto 60px auto;
            padding: 0 20px;
        }

        .result-hero {
            background: linear-gradient(120deg, #f5f8ff 0%, #eef8f4 100%);
            border: 1px solid #d9e5f3;
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 10px 24px rgba(15, 35, 70, 0.08);
            margin-bottom: 24px;
        }

        .result-title {
            margin: 0 0 8px 0;
            color: #1f2a44;
            font-size: 1.8rem;
            font-weight: 700;
        }

        .result-subtitle {
            margin: 0;
            color: #4a5878;
            font-size: 0.98rem;
        }

        .result-output {
            margin-top: 18px;
            background: #ffffff;
            border-left: 5px solid #2f80ed;
            border-radius: 10px;
            padding: 16px 18px;
            color: #1f2a44;
            line-height: 1.55;
            font-size: 1rem;
        }

        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .image-card {
            background: #fff;
            border: 1px solid #e3e9f2;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(18, 34, 66, 0.06);
        }

        .image-card img {
            width: 100%;
            height: 360px;
            object-fit: contain;
            background: #f8fbff;
        }

        .image-card .card-content {
            padding: 14px 16px;
        }

        .image-card .card-content h2 {
            margin: 0;
            font-size: 1rem;
            color: #273552;
        }

        .result-footer-note {
            margin-top: 20px;
            background: #fdf4eb;
            border: 1px solid #f6d8ba;
            border-radius: 10px;
            padding: 14px 16px;
            color: #7a4a1f;
            font-size: 0.95rem;
        }
    </style>
</head>

<body>
    <x-navbar />

    <main class="result-page">
        <section class="result-hero">
            <h1 class="result-title">Resultado da Classificação</h1>
            <p class="result-subtitle">
                Abaixo está a saída textual do algoritmo e a comparação visual entre imagem original e segmentada.
            </p>

            <div class="result-output">
                {!! $resultado !!}
            </div>

            <div class="result-footer-note">
                Esta análise é uma ferramenta de apoio técnico e não substitui a avaliação médica especializada.
            </div>
        </section>

        <section class="image-grid">
            <article class="image-card">
                <img src="{{ $imagePath }}" alt="Imagem original do exame">
                <div class="card-content">
                    <h2>Imagem Original</h2>
                </div>
            </article>

            <article class="image-card">
                <img src="{{ $imagePath }}.png" alt="Imagem segmentada pelo algoritmo">
                <div class="card-content">
                    <h2>Imagem Segmentada</h2>
                </div>
            </article>
        </section>
    </main>

    <x-footer />
</body>

</html>