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

    <title>Upload de Imagem</title>
    <script>
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

        function validateNickname(input) {
            input.value = input.value.replace(/[^a-zA-Z0-9_]/g, '');
        }

        function setMessage(message, type = 'info') {
            const statusBox = document.getElementById('status-box');
            statusBox.className = `status-box ${type}`;
            statusBox.textContent = message;
            statusBox.style.display = 'block';
        }

        function clearMessage() {
            const statusBox = document.getElementById('status-box');
            statusBox.style.display = 'none';
            statusBox.textContent = '';
        }

        function updateLocations() {
            const typeSelect = document.getElementById('type');
            const locationSelect = document.getElementById('location');
            const selectedType = typeSelect.value;
            locationSelect.innerHTML = '<option value="" disabled selected>Selecione a localização</option>';

            if (!locationsByType[selectedType]) {
                return;
            }

            locationsByType[selectedType].forEach((location) => {
                const option = document.createElement('option');
                option.value = location;
                option.textContent = locationLabels[location];
                locationSelect.appendChild(option);
            });
        }

        function updateImagePreview() {
            const fileInput = document.getElementById('input-file');
            const previewImg = document.getElementById('preview-img');
            const fileMeta = document.getElementById('file-meta');

            if (!fileInput.files || !fileInput.files[0]) {
                previewImg.src = "{{ asset('images/508-icon.png') }}";
                fileMeta.textContent = 'Nenhum arquivo selecionado.';
                return;
            }

            const file = fileInput.files[0];
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImg.src = e.target.result;
            };
            reader.readAsDataURL(file);
            fileMeta.textContent = `${file.name} - ${(file.size / (1024 * 1024)).toFixed(2)} MB`;
        }

        function validateForm() {
            const fileInput = document.getElementById('input-file');
            const typeSelect = document.getElementById('type');
            const locationSelect = document.getElementById('location');

            if (!fileInput.files || !fileInput.files[0]) {
                setMessage('Selecione uma imagem antes de enviar.', 'error');
                return false;
            }

            const file = fileInput.files[0];
            const allowedMime = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml'];
            if (!allowedMime.includes(file.type)) {
                setMessage('Formato inválido. Use JPEG, PNG, JPG, GIF ou SVG.', 'error');
                return false;
            }

            if (file.size > 2 * 1024 * 1024) {
                setMessage('A imagem excede 2 MB.', 'error');
                return false;
            }

            if (!typeSelect.value || !locationSelect.value) {
                setMessage('Selecione tipo e localização do exame.', 'error');
                return false;
            }

            return true;
        }

        async function pollAiJob(statusUrl) {
            const maxAttempts = 180;
            for (let attempt = 1; attempt <= maxAttempts; attempt++) {
                const response = await fetch(statusUrl, { headers: { 'Accept': 'application/json' } });
                if (!response.ok) {
                    throw new Error('Falha ao consultar status do processamento.');
                }

                const payload = await response.json();
                const status = payload.status;
                if (status === 'completed') {
                    const resultRoute = payload.resultRoute;
                    const encodedPath = encodeURIComponent(payload.path);
                    const encodedResult = encodeURIComponent(payload.result ?? '');
                    window.location.href = `/${resultRoute}/${encodedResult}/${encodedPath}`;
                    return;
                }

                if (status === 'failed') {
                    throw new Error(payload.error || 'Processamento falhou.');
                }

                setMessage(`Processando exame... tentativa ${attempt}`, 'info');
                await new Promise((resolve) => setTimeout(resolve, 2000));
            }

            throw new Error('Tempo limite excedido ao aguardar processamento.');
        }

        async function submitUpload(event) {
            event.preventDefault();
            clearMessage();

            if (!validateForm()) {
                return;
            }

            const form = document.getElementById('upload-image-form');
            const submitButton = document.getElementById('submit-button');
            const spinner = document.getElementById('loading-indicator');

            submitButton.disabled = true;
            spinner.style.display = 'inline-block';
            setMessage('Enviando exame para processamento...', 'info');

            const formData = new FormData(form);
            try {
                const response = await fetch('/upload', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                if (response.status === 202) {
                    const payload = await response.json();
                    setMessage('Upload concluído. Aguardando processamento assíncrono...', 'success');
                    await pollAiJob(payload.statusUrl);
                    return;
                }

                if (response.redirected) {
                    window.location.href = response.url;
                    return;
                }

                if (!response.ok) {
                    throw new Error('Não foi possível concluir o upload.');
                }

                const textResponse = await response.text();
                if (textResponse.includes('<html') || textResponse.includes('<!DOCTYPE')) {
                    document.open();
                    document.write(textResponse);
                    document.close();
                    return;
                }

                setMessage('Processamento concluído.', 'success');
            } catch (error) {
                setMessage(error.message || 'Erro ao processar o upload.', 'error');
            } finally {
                submitButton.disabled = false;
                spinner.style.display = 'none';
            }
        }

        function bootstrapPage() {
            const typeSelect = document.getElementById('type');
            const fileInput = document.getElementById('input-file');
            const form = document.getElementById('upload-image-form');

            typeSelect.addEventListener('change', updateLocations);
            fileInput.addEventListener('change', updateImagePreview);
            form.addEventListener('submit', submitUpload);
        }

        document.addEventListener('DOMContentLoaded', bootstrapPage);

        function showMenu() {
            const navLinks = document.getElementById("navLinks");
            navLinks.style.right = "0";
        }

        function hideMenu() {
            const navLinks = document.getElementById("navLinks");
            navLinks.style.right = "-200px";
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
        <h1>Upload de Imagem</h1>
    </section>

    <form id="upload-image-form" method="POST" action="/upload" enctype="multipart/form-data">
        @csrf
        <div class="hero">
            <label for="input-file" id="drop-area" class="card-panel">
                <input type="file" name="image" accept="image/*" id="input-file" hidden>
                <div id="img-view">
                    <img id="preview-img" src="{{ asset('images/508-icon.png') }}" alt="Preview">
                    <p>Clique para selecionar uma imagem médica</p>
                    <span id="file-meta" class="file-meta">Nenhum arquivo selecionado.</span>
                </div>
            </label>
        </div>
        <div class="form-container">
            <label for="type" class="form-label">Apelido:</label>
            <input type="text" class="form-text" name="nickname" id="nickname"
                placeholder="Use um apelido para identificação do resultado" oninput="validateNickname(this)">

            <label for="type" class="form-label">Tipo:</label>
            <select name="type" class="form-select" id="type">
                <option value="" disabled selected>Selecione um tipo de exame</option>
                <option value="ultrassom">Ultrassom</option>
                <option value="tomografia">Tomografia</option>
                <option value="ressonancia">Ressonância</option>
                <option value="mamografia">Mamografia</option>
                <option value="fotografia">Fotografia</option>
            </select>

            <label for="location" class="form-label">Local:</label>
            <select name="location" class="form-select" id="location">
                <option value="" disabled selected>Selecione a localização</option>
            </select>

            <div id="status-box" class="status-box" style="display: none;"></div>

            <button type="submit" id="submit-button" class="form-submit">
                Enviar para análise
                <span id="loading-indicator" class="loading-indicator" style="display: none;"></span>
            </button>
        </div>
    </form>
</body>

</html>