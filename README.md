# Portal de IA para Classificacao e Segmentacao de Cancer

Este repositorio contem um portal web para upload e analise de exames medicos com suporte a classificacao e segmentacao por modelos de deep learning.

## Documentacao principal

A documentacao tecnica completa, preparada para uso em artigo cientifico/tecnologico, esta em:

- `DOCUMENTACAO_CIENTIFICA.md`
- `MICROSERVICES.md`
- `ARTIGO_ATUALIZACAO_MICROSERVICOS.md`

## Stack do projeto

- Laravel 10 (PHP 8.1+)
- Python (TensorFlow/Keras/OpenCV)
- MySQL
- Blade + CSS/JS

## Execucao rapida

1. `composer install`
2. configurar `.env`
3. `php artisan key:generate`
4. `php artisan migrate`
5. `php artisan storage:link`
6. instalar dependencias Python
7. garantir modelos `.h5` nos caminhos esperados pelos scripts
8. `php artisan serve`

## Observacao importante

Este software e voltado a pesquisa e apoio tecnico, nao substitui avaliacao medica especializada.
