@php
$imagePath = $imagePath;
$resultado = $resultado;
$resultado=str_replace("QQQ", "<br>", $resultado);
$resultado=str_replace("__", "%", $resultado);
$resultado=str_replace("_", " ", $resultado);
@endphp

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" type="text/css" href="/css/home.css">
    <link rel="stylesheet" type="text/css" href="/css/card.css">
    <title>Home</title>
</head>

<body>
    <x-navbar />
    <div class="hero">
    <div class="card">
        <H3>Tumor Classificado Pelo Algoritmo Como:
            <div class="corAzul">
                {!! $resultado !!}
            </div> 
        </H3>
        <BR><BR>
        <H4><BR>As Anomalias Foram Destacadas na Imagem<BR>
        </H4>
        <BR>
        </div>
        <center>
            <div class="card">
                <img src={{ $imagePath }} alt='Imagem'>
                <div class="card-content">
                    <h1>Imagem Original</h1>
                </div>
            </div>
            <BR><BR><BR><BR>

            <div class="card">
                <img src={{ $imagePath }}.png alt='Imagem'>
                <div class="card-content">
                    <h1>Imagem Segmentada</h1>
                </div>
            </div>
        </center>
        <BR><BR><BR><BR>
    </div>
    <x-footer />
</body>