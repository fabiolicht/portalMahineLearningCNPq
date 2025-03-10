<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/fontawesome.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>LNCC Cancer</title>
</head>
<body>
    <section class="sub-header">
        <nav>
            <a href="/home" class="titulo">LNCC Cancer</a>
            <div class="nav-links" id="navLinks">
                <i class="fa fa-times" onclick="hideMenu()"></i>
                <ul>
                    <li><a href="/home">Home</a></li>
                    <li><a href="/upload">Upload</a></li>
                    <li><a href="/sobre">Sobre</a></li>
                    <li><a href="/contato">Contato</a></li>
                </ul>
            </div>
            <i class="fa fa-bars" onclick="showMenu()"></i>
        </nav>
        <h1>Sobre</h1>
    </section>

    <section class="about-us">
        <div class="row">
            <div class="about-col">
                <h1>Projeto de Segmentação de Imagens para Detecção de Câncer</h1>
                <p>O Projeto de Segmentação de Imagens para Detecção de Câncer é uma iniciativa inovadora que utiliza técnicas avançadas de processamento de imagens e aprendizado profundo para auxiliar no diagnóstico precoce de câncer. 
                    O objetivo principal é identificar automaticamente se um cancer é benigno ou maligno com base em imagens médicas, como radiografias, tomografias e ressonâncias magnéticas.</p>
                <a href="/upload" class="hero-btn red-btn">Testar Agora</a>
            </div>
            <div class="about-col">
                <img class="tirarImagem" src="images/squareImg3.jpg">
            </div>
        </div>
    </section>

    <section class="about-us">
        <div class="row">
            <div class="about-col">
                <h1>Como Funciona ?</h1>
                <p>Aquisição de Imagens: O projeto começa com a coleta de imagens médicas de pacientes. Essas imagens podem ser de diferentes modalidades, como mamografias, ultrassonografias ou exames de ressonância magnética.<br>
                    Pré-processamento: As imagens são pré-processadas para remover ruídos e melhorar a qualidade. Isso envolve etapas como correção de contraste, normalização e realce.<br>
                    Segmentação Automática: A parte central do projeto é a segmentação automática das lesões. Algoritmos de redes neurais convolucionais (CNNs) são treinados para identificar regiões suspeitas nas imagens. Essas regiões são então isoladas, destacando a área da lesão.<br>
                    Classificação: Após a segmentação, o sistema classifica a lesão como benigna ou maligna. Isso é feito com base em características extraídas da imagem, como forma, textura e intensidade dos pixels.<br>
                    Resultados e Auxílio ao Médico: Os resultados são apresentados aos médicos, que podem usar essas informações para tomar decisões mais precisas sobre o tratamento. A IA não substitui o médico, mas atua como uma ferramenta de apoio.</p>
            </div>
            <div class="about-col">
                <h1>Benefícios e Desafios</h1>
                <p>Benefícios:<br>
                    Detecção Precoce: A segmentação automática permite a detecção precoce de lesões, aumentando as chances de sucesso no tratamento.
                    Redução de Erros: A IA ajuda a minimizar erros humanos, melhorando a precisão do diagnóstico.
                    Eficiência: O processo automatizado economiza tempo e recursos.<br>
                    Desafios:<br>
                    Precisão: Garantir alta precisão na segmentação e classificação das lesões.
                    Generalização: Adaptar o modelo para diferentes tipos de câncer e modalidades de imagem.
                    Interpretabilidade: Tornar os resultados compreensíveis para os médicos.</p>
                    <p>O Projeto de Segmentação de Imagens para Detecção de Câncer é uma promissora aplicação da inteligência artificial na área médica. Com o avanço contínuo da tecnologia, esperamos que essa abordagem contribua significativamente para o diagnóstico precoce e o tratamento eficaz de pacientes com câncer.</p>
            </div>
        </div>
    </section>
<script>
    var navLinks = document.getElementById("navLinks");
    function showMenu() {
        navLinks.style.right = "0";
    }
    function hideMenu() {
        navLinks.style.right = "-200px";
    }
</script>
</body>
</html>