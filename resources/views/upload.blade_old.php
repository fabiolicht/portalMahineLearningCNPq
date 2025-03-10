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
    <link rel="stylesheet" type="text/css" href="/css/upload.css">
    <title>Upload</title>
</head>
 
<body>
    <section class="sub-headerU">
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
        <h1>Upload</h1>
    </section>

    <form method="POST" action="/upload" enctype="multipart/form-data">
        @csrf
        <div class="hero">
            <label for="input-file" id="drop-area">
                <input type="file" name="image" accept="image/*" id="input-file" hidden draggable="true"
                    ondragstart="event.dataTransfer.setData('text/plain',null)" onchange="handleFileSelect()">
                <div id="img-view">
                    <img src="{{ asset('images/508-icon.png') }}">
                    <p>Clique aqui para o upload</p>
                </div>
            </label>
        </div>
        <div class="form-container">
            <label for="type" class="form-label">Apelido:</label>
            <input type="text" class="form-text" name="nickname" id="nickname" placeholder="Use um apelido para identificação do resultado"> </input>
            
            <label for="type" class="form-label">Tipo:</label>
            <select name="type" class="form-select">
                <option value="celular">Análise Celular</option>
                <option value="mamografia">Mamografia</option>
                <option value="ultrassom">Ultrassom</option>
                <option value="ressonancia">Ressonância</option>
                <option value="tomografia">Tomografia</option>
                <option value="raio_x">Raio X</option>
            </select>
            <label for="location" class="form-label">Local:</label>
            <select name="location" class="form-select"> 
                <option value="abdomen">Abdômen</option>
                <option value="cerebro">Cérebro</option>
                <option value="utero">Útero</option>
                <option value="colon">Cólon</option>
                <option value="estomago">Estômago</option>
                <option value="mama">Mama</option>
                <option value="oral">Oral</option>
                <option value="pulmao">Pulmão</option>
                <option value="rim">Rim</option>
                <option value="outras_localizacoes">Outras Localizações</option>
            </select>
            <input type="submit" value="Upload" class="form-submit">
        </div>
    </form>
    @if ($errors->any())
    <script>
    alert("Erros encontrados:\n{!! addslashes(implode('\n', $errors->all())) !!}");
    </script>
    @endif
    <script>
        var navLinks = document.getElementById("navLinks");
        function showMenu() {
            navLinks.style.right = "0";
        }
        function hideMenu() {
            navLinks.style.right = "-200px";
        }
    </script>
    <script src="{{ asset('js/upload.js') }}"></script>
</body>

</html>