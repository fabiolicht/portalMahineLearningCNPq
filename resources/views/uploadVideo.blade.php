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

    <title>Upload de Vídeo</title>

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

            if (locationsByType[selectedType]) {
                locationsByType[selectedType].forEach(location => {
                    const option = document.createElement('option');
                    option.value = location;
                    option.textContent = locationLabels[location];
                    locationSelect.appendChild(option);
                });
            }
        }

        function updateVideoPreview() {
            const file = document.getElementById("input-file").files[0];
            const preview = document.getElementById("preview-video");
            const fileMeta = document.getElementById('file-meta');
            if (!file) {
                preview.removeAttribute('src');
                fileMeta.textContent = 'Nenhum arquivo selecionado.';
                return;
            }
            const url = URL.createObjectURL(file);
            preview.src = url;
            preview.style.display = "block";
            fileMeta.textContent = `${file.name} - ${(file.size / (1024 * 1024)).toFixed(2)} MB`;
        }

        function setStartTime() {
            const video = document.getElementById('preview-video');
            const current = video.currentTime.toFixed(2);
            document.getElementById('start_time').value = current;
            document.getElementById('start-time-label').textContent = `Início: ${current} s`;
            updateTimeInfo();
        }

        function setEndTime() {
            const video = document.getElementById('preview-video');
            const current = video.currentTime.toFixed(2);
            document.getElementById('end_time').value = current;
            document.getElementById('end-time-label').textContent = `Fim: ${current} s`;
            updateTimeInfo();
        }

        function updateTimeInfo() {
            const start = document.getElementById('start_time').value;
            const end = document.getElementById('end_time').value;
            if (start && end) {
                document.getElementById('time-info').textContent = `Trecho selecionado: de ${start}s até ${end}s`;
            }
        }

        function validateForm() {
            const fileInput = document.getElementById('input-file');
            const typeSelect = document.getElementById('type');
            const locationSelect = document.getElementById('location');

            if (!fileInput.files || !fileInput.files[0]) {
                setMessage('Selecione um vídeo antes de enviar.', 'error');
                return false;
            }

            const file = fileInput.files[0];
            const allowedMime = ['video/mpeg', 'video/mpg', 'video/mp4', 'video/x-matroska', 'video/webm'];
            if (!allowedMime.includes(file.type)) {
                setMessage('Formato inválido. Use MPEG, MPG, MP4, MKV ou WEBM.', 'error');
                return false;
            }

            if (file.size > 150000 * 1024) {
                setMessage('O vídeo excede o limite permitido.', 'error');
                return false;
            }

            if (!typeSelect.value || !locationSelect.value) {
                setMessage('Selecione tipo e localização do exame.', 'error');
                return false;
            }

            return true;
        }

        async function pollAiJob(statusUrl) {
            const maxAttempts = 240;
            for (let attempt = 1; attempt <= maxAttempts; attempt++) {
                const response = await fetch(statusUrl, { headers: { 'Accept': 'application/json' } });
                if (!response.ok) {
                    throw new Error('Falha ao consultar status do processamento.');
                }
                const payload = await response.json();

                if (payload.status === 'completed') {
                    const resultRoute = payload.resultRoute;
                    const encodedPath = encodeURIComponent(payload.path);
                    const encodedResult = encodeURIComponent(payload.result ?? '');
                    window.location.href = `/${resultRoute}/${encodedResult}/${encodedPath}`;
                    return;
                }

                if (payload.status === 'failed') {
                    throw new Error(payload.error || 'Processamento falhou.');
                }

                setMessage(`Processando vídeo... tentativa ${attempt}`, 'info');
                await new Promise((resolve) => setTimeout(resolve, 2500));
            }

            throw new Error('Tempo limite excedido ao aguardar processamento.');
        }

        async function submitUpload(event) {
            event.preventDefault();
            clearMessage();
            if (!validateForm()) {
                return;
            }

            const form = document.getElementById('upload-video-form');
            const submitButton = document.getElementById('submit-button');
            const spinner = document.getElementById('loading-indicator');
            const formData = new FormData(form);

            submitButton.disabled = true;
            spinner.style.display = 'inline-block';
            setMessage('Enviando vídeo para processamento...', 'info');

            try {
                const response = await fetch('/uploadV', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                if (response.status === 202) {
                    const payload = await response.json();
                    setMessage('Upload concluído. Processamento assíncrono iniciado...', 'success');
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
                setMessage(error.message || 'Erro ao processar upload de vídeo.', 'error');
            } finally {
                submitButton.disabled = false;
                spinner.style.display = 'none';
            }
        }

        function bootstrapPage() {
            document.getElementById('type').addEventListener('change', updateLocations);
            document.getElementById('input-file').addEventListener('change', updateVideoPreview);
            document.getElementById('upload-video-form').addEventListener('submit', submitUpload);
        }

        function showMenu() {
            const navLinks = document.getElementById("navLinks");
            navLinks.style.right = "0";
        }

        function hideMenu() {
            const navLinks = document.getElementById("navLinks");
            navLinks.style.right = "-200px";
        }

        document.addEventListener('DOMContentLoaded', bootstrapPage);

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
        <h1>Upload de Vídeo</h1>
    </section>

    <form id="upload-video-form" method="POST" action="/uploadV" enctype="multipart/form-data">
        @csrf
        <div class="hero">
            <label for="input-file" id="drop-area" class="card-panel">
                <input type="file" name="video" accept="video/*" id="input-file" hidden>
                <div id="img-view">
                    <video id="preview-video" controls></video>
                    <span id="file-meta" class="file-meta">Nenhum arquivo selecionado.</span>

                    <div class="video-marker-container">
                        <div class="video-marker-item">
                            <button type="button" class="marker-button" onclick="setStartTime()">Marcar Início</button>
                            <label id="start-time-label">Início: -- s</label>
                        </div>
                        <div class="video-marker-item">
                            <button type="button" class="marker-button" onclick="setEndTime()">Marcar Fim</button>
                            <label id="end-time-label">Fim: -- s</label>
                        </div>
                    </div>

                    <input type="hidden" name="start_time" id="start_time">
                    <input type="hidden" name="end_time" id="end_time">

                    <p id="time-info"></p>
                    <p>Clique para selecionar um vídeo do exame</p>
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