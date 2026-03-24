# Texto para Artigo: Atualizacao Arquitetural com Microservicos

## Secao pronta para Metodologia (insercao direta)

Nesta fase de evolucao do sistema, a arquitetura monolitica orientada a execucao local de scripts foi migrada para um modelo de microservicos com orquestracao via gateway. O backend Laravel foi mantido como camada de entrada (BFF/API Gateway), responsavel por validacao de upload, persistencia de metadados e coordenacao do fluxo de inferencia. A etapa de classificacao passou a ser executada por um servico dedicado em FastAPI (`classification-service`), acessado por contrato HTTP versionado (`POST /v1/classify`). Em paralelo, foi introduzido um servico de segmentacao (`segmentation-service`) com contrato inicial (`POST /v1/segment`), permitindo desacoplamento progressivo do processamento visual.

Para reduzir latencia de requisicao e aumentar robustez operacional, foi incorporado processamento assincrono com fila Redis e workers Laravel. No modo assincrono, o upload retorna imediatamente `202 Accepted`, juntamente com `requestId` e endpoint de status (`GET /ai-jobs/{requestId}`). O estado de processamento e rastreado na tabela `ai_jobs` por meio das fases `queued`, `processing`, `completed` e `failed`. Esse mecanismo elimina a dependencia de espera ativa no ciclo HTTP e melhora escalabilidade sob carga.

A estrategia de migracao preservou compatibilidade retroativa: em caso de indisponibilidade do microservico, o gateway pode executar fallback local dos scripts Python. Essa decisao reduz risco de indisponibilidade durante a transicao arquitetural e facilita validacao incremental dos novos componentes.

## Secao pronta para Contribuicoes Tecnicas

As principais contribuicoes tecnicas desta atualizacao sao:

1. desacoplamento da inferencia de IA em servicos independentes;
2. padronizacao de contrato de comunicacao entre gateway e servicos;
3. introducao de execucao assincrona orientada a filas;
4. rastreabilidade de ciclo de inferencia via entidade persistida de jobs;
5. base inicial de testes unitarios para camada de integracao HTTP.

## Secao pronta para Reprodutibilidade

Para reproducao da arquitetura proposta, foi disponibilizado ambiente local em `docker-compose` com os servicos `classification-service`, `segmentation-service`, `redis` e `mysql`. A ativacao do pipeline distribuido e controlada por variaveis de ambiente (`MEDICAL_AI_MICROSERVICE_ENABLED`, `MEDICAL_AI_ASYNC_ENABLED`, `QUEUE_CONNECTION=redis`), permitindo comparacao entre execucao sincrona e assincrona no mesmo codigo-base. Essa abordagem favorece reprodutibilidade experimental e analise de desempenho em diferentes cenarios operacionais.

## Secao pronta para Limitacoes e Trabalhos Futuros

Embora a classificacao ja esteja operacional em microservico, a segmentacao distribuida encontra-se em etapa inicial de integracao (contrato e stub funcional). Como trabalho futuro, recomenda-se completar a cadeia assincrona de segmentacao, adicionar testes E2E automatizados no ambiente `docker-compose`, e incluir observabilidade com metricas de latencia por etapa e taxa de falhas por servico.
