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
    <script>
        // Função para validar o apelido
        function validateNickname(input) {
            input.value = input.value.replace(/[^a-zA-Z0-9_]/g, '');
        }


        // Função para exibir o campo de upload de imagem
        function showFileInput() {
            const fileInputLabel = document.getElementById('drop-area');
            const typeSelect = document.getElementById('type').value;
            if (typeSelect) {
                fileInputLabel.style.display = 'block'; // Exibe o campo de upload
                updateLocations(); // Atualiza os locais com base no tipo
            } else {
                fileInputLabel.style.display = 'none'; // Esconde o campo de upload
            }
        }

        /*function handleFileSelect() {
            const fileInput = document.getElementById('input-file');
            const formData = new FormData();
            formData.append('image', fileInput.files[0]);
            const typeSelect = document.getElementById('type').value; // Pegar o tipo de exame selecionado
            formData.append('type', typeSelect); // Enviar o tipo de exame junto
            // Envia a imagem para o backend para validação
            fetch('/validate-image', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                //echo(response);
                .then(result => {
                    if (result.valid) {
                        alert('Imagem de exame médico válida!');
                    } else {
                        alert('A imagem não é um exame médico permitido.');
                    }
                })
                .catch(error => {
                    console.error('Erro ao validar a imagem:', error);
                });
        }*/

        // Função para enviar a imagem para validação sem travar a interface
        /*async function handleFileSelect() {
            try {
                const fileInput = document.getElementById('input-file');
                const formData = new FormData();
                formData.append('image', fileInput.files[0]);
                const typeSelect = document.getElementById('type').value;
                formData.append('type', typeSelect);

                // Obtém o token CSRF e o adiciona ao formData
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                formData.append('_token', csrfToken);

                // Envia a imagem para o backend para validação
                const response = await fetch('/validate-image', {
                    method: 'POST',
                    body: formData,
                });

                // Verifica se a resposta foi recebida com sucesso
                if (!response.ok) {
                    console.error('Status da resposta:', response.status);
                    console.error('Texto da resposta:', await response.text());
                    throw new Error(`Erro na resposta do servidor. Status: ${response.status}`);
                }

                const result = await response.json();
                //alert('teste: ' + JSON.stringify(result)); // Para depuração
                if (result.valid) {
                    alert('Imagem válida!\nResultado do script: ' + result.valid);
                } else {
                    alert('Imagem inválida.\nResultado do script: ' + result.valid);
                }
            } catch (error) {
                console.error('Erro ao validar a imagem:', error);
                alert('Erro ao validar a imagem: ' + error.message);
            }
        }
*/
        async function handleFileSelect() {
            try {
                const fileInput = document.getElementById('input-file');
                const previewImg = document.getElementById('preview-img');

                // Atualiza a visualização da imagem selecionada
                if (fileInput.files && fileInput.files[0]) {
                    const file = fileInput.files[0];
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        previewImg.src = e.target.result; // Atualiza o src da imagem
                    };
                    reader.readAsDataURL(file); // Lê o arquivo como base64
                }

                // Cria o FormData e adiciona os dados necessários
                const formData = new FormData();
                formData.append('image', fileInput.files[0]);
                const typeSelect = document.getElementById('type').value;
                formData.append('type', typeSelect);

                // Obtém o token CSRF
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                formData.append('_token', csrfToken);

                // Envia a imagem para o backend para validação
                const response = await fetch('/validate-image', {
                    method: 'POST',
                    body: formData,
                });

                // Verifica se a resposta foi recebida com sucesso
                if (!response.ok) {
                    console.error('Status da resposta:', response.status);
                    console.error('Texto da resposta:', await response.text());
                    throw new Error(`Erro na resposta do servidor. Status: ${response.status}`);
                }

                const result = await response.json();
                // Aqui você pode lidar com o resultado da validação
                /*if (result.valid) {
                    alert('Imagem válida!');
                } else {
                    alert('Imagem inválida.');
                }*/
            } catch (error) {
                console.error('Erro ao validar a imagem:', error);
                alert('Erro ao validar a imagem: ' + error.message);
            }
        }




        // Função para atualizar os locais conforme o tipo de exame
        function updateLocations() {
            const typeSelect = document.getElementById('type').value;
            const locationSelect = document.getElementById('location');

            // Limpa as opções anteriores
            locationSelect.innerHTML = '';

            // Mapeia os locais possíveis para o tipo de exame selecionado
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

            // Atualiza as opções de local com base no tipo
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

    <div class="button-container">
        <h2>O que você quer enviar?</h2>

        <a href="{{ route('uploadImagem') }}">
            <button class="upload-button">Upload de Imagem</button>
        </a>

        <a href="{{ route('uploadVideo') }}">
            <button class="upload-button">Upload de Vídeo</button>
        </a>
    </div>
</body>

</html>