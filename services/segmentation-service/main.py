from fastapi import FastAPI
from pydantic import BaseModel

app = FastAPI(title="segmentation-service", version="1.0.0")


class SegmentRequest(BaseModel):
    examType: str
    location: str
    filePath: str
    predictedClass: str
    requestId: str | None = None


@app.get("/health")
def health():
    return {"status": "ok"}


@app.post("/v1/segment")
def segment(payload: SegmentRequest):
    # Stub inicial da Fase A: contrato pronto para integrar segmentacao dedicada.
    return {
        "status": "ok",
        "requestId": payload.requestId,
        "outputPath": f"{payload.filePath}.png",
        "error": None,
    }
