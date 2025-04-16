<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="/css/app.css">
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/fontawesome.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/css/upload.css">

    <title>Upload</title>
    <!-- ... (head permanece igual até a tag <script>) -->

    <script>
        function validateNickname(input) {
            input.value = input.value.replace(/[^a-zA-Z0-9_]/g, '');
        }

        function showFileInput() {
            const fileInputLabel = document.getElementById('drop-area');
            const typeSelect = document.getElementById('type').value;
            if (typeSelect) {
                fileInputLabel.style.display = 'block';
                updateLocations();
            } else {
                fileInputLabel.style.display = 'none';
            }
        }

        async function handleFileSelect() {
            try {
                const fileInput = document.getElementById('input-file');
                const previewVideo = document.getElementById('preview-video');

                if (fileInput.files && fileInput.files[0]) {
                    const file = fileInput.files[0];
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        previewVideo.src = e.target.result;
                        previewVideo.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }

                const formData = new FormData();
                formData.append('video', fileInput.files[0]);
                const typeSelect = document.getElementById('type').value;
                formData.append('type', typeSelect);

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                formData.append('_token', csrfToken);

                /*
                const response = await fetch('/validate-video', {
                    method: 'POST',
                    body: formData,
                });
                

                if (!response.ok) {
                    console.error('Status da resposta:', response.status);
                    console.error('Texto da resposta:', await response.text());
                    throw new Error(`Erro na resposta do servidor. Status: ${response.status}`);
                }
                

                const result = await response.json();
                */
            } catch (error) {
                //console.error('Erro ao validar o vídeo:', error);
                //alert('Erro ao validar o vídeo: ' + error.message);
            }
        }

        function updateLocations() {
            const typeSelect = document.getElementById('type').value;
            const locationSelect = document.getElementById('location');
            locationSelect.innerHTML = '';

            const locationsByType = {
                celular: ["cervical", "colon", "mama", "oral", "pulmao", "rim", "utero", "outras_localizacoes"],
                mamografia: ["mama"],
                ultrassom: ["figado", "mama", "outras_localizacoes"],
                ressonancia: ["outras_localizacoes"],
                tomografia: ["abdomen", "cerebro", "figado", "rim", "outras_localizacoes"],
                raio_x: ["pulmao", "outras_localizacoes"],
                fotografia: ["pele"]
            };

            const locationLabels = {
                abdomen: "Abdômen",
                cervical: "Cervical",
                cerebro: "Cérebro",
                figado: "Fígado",
                utero: "Útero",
                colon: "Cólon",
                mama: "Mama",
                oral: "Oral",
                pulmao: "Pulmão",
                rim: "Rim",
                pele: "Pele",
                outras_localizacoes: "Outras Localizações"
            };

            if (locationsByType[typeSelect]) {
                locationsByType[typeSelect].forEach(location => {
                    const option = document.createElement('option');
                    option.value = location;
                    option.textContent = locationLabels[location];
                    locationSelect.appendChild(option);
                });
            } else {
                console.error('Nenhuma localização encontrada para o tipo selecionado:', typeSelect);
            }
        }
    </script>
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

    <form method="POST" action="/uploadV" enctype="multipart/form-data">
        @csrf
        <div class="hero">
            <label for="input-file" id="drop-area">
                <input type="file" name="video" accept="video/*" id="input-file" hidden onchange="handleFileSelect()">
                <div id="img-view">
                    <video id="preview-video" controls style="max-width: 100%; display: none;"></video>
                    <p>Clique aqui para o upload de vídeo</p>
                </div>
            </label>
        </div>
        <div class="form-container">
            <label for="type" class="form-label">Apelido:</label>
            <input type="text" class="form-text" name="nickname" id="nickname"
                placeholder="Use um apelido para identificação do resultado" oninput="validateNickname(this)">

            <label for="type" class="form-label">Tipo:</label>
            <select name="type" class="form-select" id="type" onchange="showFileInput()">
                <option value="" disabled selected>Selecione um tipo de exame</option>
                <option value="ultrassom">Ultrassom</option>
                <option value="tomografia">Tomografia</option>
                <option value="ressonancia">Ressonância</option>
                <option value="mamografia">Mamografia</option>
                <option value="fotografia">Fotografia</option>
            </select>

            <label for="location" class="form-label">Local:</label>
            <select name="location" class="form-select" id="location">
                <!-- Atualizado via JS -->
            </select>

            <input type="submit" value="Upload" class="form-submit">
        </div>
    </form>
</body>

</html>