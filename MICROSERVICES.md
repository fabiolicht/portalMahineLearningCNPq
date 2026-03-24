# Guia de Microservicos (Fase A/B)

Este projeto agora suporta integracao inicial com microservicos de IA.

## Servicos

- `classification-service` (FastAPI): endpoint `POST /v1/classify`
- `segmentation-service` (FastAPI): endpoint `POST /v1/segment` (stub inicial)
- `redis`: fila/base para proxima fase assincrona
- `mysql`: banco do gateway Laravel

## Subir infraestrutura local

```bash
docker compose up --build
```

## Configuracao no Laravel

Adicionar no `.env`:

```env
MEDICAL_AI_MICROSERVICE_ENABLED=true
MEDICAL_AI_ASYNC_ENABLED=true
MEDICAL_AI_CLASSIFICATION_URL=http://classification-service:8001
MEDICAL_AI_SEGMENTATION_URL=http://segmentation-service:8002
MEDICAL_AI_TIMEOUT=600
```

Quando `MEDICAL_AI_MICROSERVICE_ENABLED=true`, o gateway tenta chamar o microservico.
Se ocorrer erro, ha fallback para execucao local dos scripts Python (compatibilidade retroativa).

Quando `MEDICAL_AI_ASYNC_ENABLED=true`, o upload retorna `202 Accepted` com `requestId` e `statusUrl`.

Endpoint de status:

- `GET /ai-jobs/{requestId}`

## Contrato atual de classificacao

### Requisicao

`POST /v1/classify`

```json
{
  "examType": "mamografia",
  "location": "mama",
  "filePath": "public/images/nick/exame.png",
  "requestId": "req_123"
}
```

### Resposta

```json
{
  "status": "ok",
  "requestId": "req_123",
  "prediction": "Tumor_MalignoQQQHá_90.00___de_ser_Tumor_Maligno",
  "error": null
}
```
