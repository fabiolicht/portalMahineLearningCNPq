# Documentacao Tecnico-Cientifica do Portal de IA para Deteccao de Cancer

## 1. Visao Geral

Este projeto implementa um portal web para apoio a classificacao e segmentacao de imagens medicas com foco em deteccao de cancer. A aplicacao combina:

- backend em Laravel (PHP) para fluxo web, persistencia e orquestracao;
- scripts em Python/TensorFlow para inferencia dos modelos de classificacao;
- scripts em Python/OpenCV para geracao de mascaras segmentadas;
- suporte a multiplas modalidades de exame (imagem e video, conforme disponibilidade de modelos).

O sistema nao substitui avaliacao medica. O uso e de apoio a pesquisa e triagem experimental.

## 2. Objetivo Cientifico e Tecnologico

O objetivo central e avaliar um pipeline integrado de IA para:

1. receber exames medicos via interface web;
2. classificar automaticamente achados (ex.: normal, tumor benigno, tumor maligno);
3. gerar realce visual da area de interesse por segmentacao;
4. apresentar resultado textual e visual de forma rastreavel.

Do ponto de vista tecnologico, o projeto demonstra a integracao de modelos de deep learning em producao web com saida interpretavel para o usuario final.

## 3. Escopo Funcional Atual

### 3.1 Modalidades e anatomias suportadas no fluxo de imagem

O controlador principal (`PhotoController`) roteia para scripts especificos de classificacao conforme combinacao `tipo` + `localizacao`.

Coberturas observadas no codigo:

- ultrassom mama;
- mamografia mama;
- tomografia figado, rim, cerebro, abdomen;
- ultrassom figado;
- celular cervical, colon, mama, oral, pulmao, rim, utero;
- fotografia pele.

Em caso nao mapeado, o sistema retorna mensagem de nao suporte.

### 3.2 Fluxo de video

O controlador de video (`VideoController`) contem rota para `ultrassom + mama` com redirecionamento para classificacao especifica de video.

## 4. Arquitetura do Sistema

## 4.1 Camadas

- **Apresentacao (Blade/CSS/JS)**: telas de home, upload, sobre, resultado.
- **Aplicacao (Laravel Controllers/Routes)**: validacao de upload, persistencia no banco e execucao de scripts Python.
- **Inferencia (Python)**: carregamento de modelos `.h5` para classificacao e segmentacao.
- **Persistencia (MySQL via Eloquent)**: metadados de arquivos enviados (nome, tamanho, tipo, caminho, timestamps).
- **Armazenamento de arquivos**: disco local em `storage/app/public`.

## 4.2 Fluxo ponta a ponta (imagem)

1. Usuario envia imagem na tela de upload.
2. Laravel valida MIME/tamanho e campos de contexto.
3. Arquivo e salvo em `storage/app/public/images/<apelido>/`.
4. Metadados sao inseridos na tabela `photo`.
5. Controller chama script Python de classificacao.
6. Saida textual da classificacao e retornada ao Laravel.
7. Usuario e redirecionado para rota de resultado.
8. Controller de resultado chama script de segmentacao.
9. Script gera imagem sobreposta (`.png`) no mesmo prefixo de caminho.
10. View `resultado` exibe imagem original + imagem segmentada + texto de classe/probabilidade.

## 4.3 Fluxo ponta a ponta (video)

1. Usuario envia video na tela de upload de video.
2. Laravel valida arquivo e salva em `storage/app/public/videos/<apelido>/`.
3. Metadados sao inseridos na tabela `video`.
4. Controller chama script de classificacao de video (quando suportado).
5. Usuario e redirecionado para pagina de resultado dedicada.

## 5. Estrutura do Repositorio (componentes relevantes)

- `routes/web.php`: definicao das rotas HTTP e mapeamento para controladores.
- `app/Http/Controllers/PhotoController.php`: upload de imagem e disparo da classificacao.
- `app/Http/Controllers/VideoController.php`: upload de video e disparo da classificacao.
- `app/Http/Controllers/ResultController*.php`: pos-processamento e segmentacao por modalidade.
- `app/Models/Photo.php`, `app/Models/video.php`: entidades persistidas.
- `database/migrations/*photo*`, `*video*`: esquema de banco para metadados.
- `resources/views/*.blade.php`: telas e apresentacao dos resultados.
- `public/classificacao/*.py`: scripts de classificacao.
- `public/segmentacao/*.py`: scripts de segmentacao.
- `public/validaImagem.py`: validacao preliminar de imagem por modelo.
- `install.txt`, `pipList.txt`: anotacoes de instalacao/dependencias observadas.

## 6. Mapeamento de Endpoints Principais

### 6.1 Navegacao

- `GET /` e `GET /home`: pagina inicial.
- `GET /sobre`: descricao do projeto.
- `GET /contato`: contato.
- `GET /upload`: selecao de tipo de upload.
- `GET /uploadImagem`: formulario de upload de imagem.
- `GET /uploadVideo`: formulario de upload de video.

### 6.2 Processamento

- `POST /upload`: envio de imagem + inferencia/classificacao.
- `POST /uploadV`: envio de video + inferencia/classificacao.
- `POST /validate-image`: validacao preliminar de imagem.

### 6.3 Resultados por dominio

Exemplos de rotas de resultado (cada uma associada a um `ResultController`):

- `classificationM`: mamografia;
- `classificationUM`: ultrassom mama;
- `classificationTF`: tomografia figado;
- `classificationUF`: ultrassom figado;
- `classificationCCv`, `classificationCC`, `classificationCM`, `classificationCO`, `classificationCP`, `classificationCR`, `classificationCU`: exames celulares;
- `classificationTR`, `classificationTA`, `classificationTC`: tomografias;
- `classificationFP`: fotografia de pele;
- `classificationUMV`: ultrassom mama em video.

## 7. Modelo de Dados

### 7.1 Tabela `photo`

Campos:

- `id` (PK);
- `name` (nome original do arquivo);
- `size` (tamanho em bytes);
- `type` (tipo de exame);
- `location` (caminho salvo no storage);
- `created_at`, `updated_at`.

### 7.2 Tabela `video`

Campos:

- `id` (PK);
- `name` (nome original do arquivo);
- `size` (tamanho em bytes);
- `type` (tipo de exame);
- `location` (caminho salvo no storage);
- `created_at`, `updated_at`.

## 8. Dependencias e Requisitos

## 8.1 Backend web

- PHP `^8.1`;
- Laravel `^10.10`;
- MySQL (ou banco compativel configurado no `.env`);
- Composer;
- Node.js (apenas para assets com Vite, se necessario).

Dependencias PHP relevantes:

- `laravel/framework`;
- `intervention/image`;
- `guzzlehttp/guzzle`.

## 8.2 Stack de IA (Python)

Bibliotecas observadas:

- `tensorflow`, `keras`, `numpy`;
- `opencv-python`;
- `pandas`, `joblib` (uso potencial por scripts auxiliares).

O arquivo `pipList.txt` mostra ambiente extenso (inclusive GPU/CUDA), indicando execucao em contexto de pesquisa com aceleracao opcional.

## 8.3 Artefatos de modelo

Os scripts fazem referencia a modelos `.h5` fora de `public`, por caminhos relativos (ex.: `../../segmentacaoBenignoMamografia.h5`, `../../../classificacaoMamografia.h5`).

Para reproducao, esses artefatos devem estar presentes nas localizacoes esperadas.

## 9. Procedimento de Instalacao e Execucao

## 9.1 Passos recomendados

1. Instalar dependencias PHP:
   - `composer install`
2. Configurar ambiente Laravel:
   - copiar `.env` a partir de `.env.example` (se necessario);
   - ajustar variaveis de banco e app;
   - `php artisan key:generate`
3. Criar banco e aplicar migracoes:
   - `php artisan migrate`
4. Preparar storage:
   - `php artisan storage:link`
5. Instalar dependencias Python (em ambiente virtual):
   - `pip install tensorflow keras numpy opencv-python pandas joblib`
6. Garantir modelos `.h5` nos caminhos usados pelos scripts.
7. Subir servidor:
   - `php artisan serve`

## 9.2 Observacoes operacionais

- O codigo tenta localizar `python3` com `which` (Linux/macOS) ou `where` (Windows).
- O processamento e sincrono no request HTTP; inferencias longas podem impactar latencia percebida pelo usuario.

## 10. Metodologia de Processamento

## 10.1 Classificacao

Padrao observado nos scripts:

1. carregar modelo Keras `.h5`;
2. redimensionar entrada para `256x256`;
3. converter para `float32`;
4. executar inferencia;
5. mapear `argmax` para classe clinica;
6. imprimir resultado em string processada pelo PHP.

Exemplo de classes em mamografia:

- `Normal`;
- `Tumor_Maligno`;
- `Tumor_Benigno`.

## 10.2 Segmentacao

Padrao observado:

1. carregar modelo de segmentacao;
2. gerar mascara por threshold (`> 0.5`);
3. colorir regiao segmentada;
4. sobrepor na imagem original com transparencia;
5. salvar saida `<caminho_original>.png`.

## 10.3 Apresentacao de resultados

A view de resultado:

- renderiza texto de classificacao;
- mostra imagem original;
- mostra imagem segmentada gerada no passo anterior.

## 11. Reprodutibilidade para Artigo

Para uso em publicacao, recomenda-se registrar:

1. versao do codigo (commit hash);
2. hash e origem dos modelos `.h5`;
3. ambiente de execucao (SO, Python, CUDA, TensorFlow, PHP);
4. conjunto de dados de avaliacao (criterios de inclusao/exclusao);
5. protocolo de split (treino/validacao/teste ou validacao externa);
6. metricas por classe (acuracia, sensibilidade, especificidade, AUC, F1);
7. intervalo de confianca e teste estatistico quando aplicavel.

## 11.1 Template minimo de secao experimental

- **Dados**: descricao da base, modalidade, tamanho amostral e balanceamento.
- **Pre-processamento**: redimensionamento, normalizacao, augmentations.
- **Modelos**: arquitetura, hiperparametros, criterio de parada.
- **Avaliacao**: metricas primarias/secundarias e estrategia de validacao.
- **Implementacao**: hardware, software e tempo de inferencia.
- **Interpretabilidade**: forma de visualizacao da segmentacao e exemplos.

## 12. Riscos, Limitacoes e Ameacas a Validade

### 12.1 Limitacoes tecnicas observadas

- Dependencia de caminhos relativos para modelos, sensivel a estrutura de pastas.
- Possivel divergencia de nomes de scripts (ex.: referencia a script de video nao encontrado).
- Processamento sincrono no ciclo HTTP, com risco de timeout em inferencias pesadas.
- Validacao de imagem via endpoint dedicado com inconsistencias de retorno (campo `valid`).
- Ausencia de suite abrangente de testes para fluxos clinicos e scripts Python.

### 12.2 Ameacas a validade cientifica

- Generalizacao limitada sem validacao multicentrica.
- Potencial desbalanceamento de classes por modalidade.
- Risco de leakage se separacao treino/teste nao for estritamente por paciente.
- Falta de calibracao probabilistica pode afetar interpretacao clinica.

### 12.3 Aspectos eticos e regulatorios

- Necessidade de anonimizar dados medicos (LGPD).
- Consentimento e governanca de dados.
- O sistema deve ser tratado como apoio a decisao, nao diagnostico autonomo.

## 13. Diretrizes para Evolucao Tecnica

1. Migrar inferencia para fila assicrona (Laravel Queues).
2. Externalizar configuracao de modelos para arquivo de mapeamento.
3. Padronizar protocolo de saida JSON dos scripts Python.
4. Adicionar testes automatizados (feature tests + validacao de inferencia mockada).
5. Implementar observabilidade (logs estruturados, tempos por etapa, taxa de erro).
6. Criar endpoint de versao de modelo para rastreabilidade cientifica.

## 14. Checklist para Submissao de Artigo

- [ ] Definir pergunta de pesquisa e hipotese.
- [ ] Descrever populacao, modalidade e criterios de selecao.
- [ ] Relatar pipeline completo com diagrama arquitetural.
- [ ] Informar detalhes de treinamento e avaliacao dos modelos.
- [ ] Apresentar metricas com intervalo de confianca.
- [ ] Incluir exemplos visuais de segmentacao correta/erro.
- [ ] Declarar limitacoes e aspectos eticos.
- [ ] Disponibilizar versao reproduzivel do codigo e ambiente.

## 15. Citacao sugerida do software (modelo)

Use o seguinte formato no artigo (adaptar autores/ano/versao):

`Autor(es). Portal de IA para Classificacao e Segmentacao de Imagens Medicas. Versao X.Y, Ano. Software de pesquisa.`

---

Este documento foi elaborado com base na estrutura e implementacao atual do repositorio, com foco em apoiar redacao de artigo cientifico/tecnologico e reproducao tecnica do sistema.
