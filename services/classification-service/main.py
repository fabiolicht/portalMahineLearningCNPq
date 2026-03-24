from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import subprocess
import os

app = FastAPI(title="classification-service", version="1.0.0")


class ClassifyRequest(BaseModel):
    examType: str
    location: str
    filePath: str
    requestId: str | None = None


ROUTE_MAP = {
    ("ultrassom", "mama"): "classificacao/classificacaoUltrassomMama.py",
    ("mamografia", "*"): "classificacao/classificacaoMamografia.py",
    ("tomografia", "figado"): "classificacao/classificacaoTomografiaFigado.py",
    ("ultrassom", "figado"): "classificacao/classificacaoUltrassomFigado.py",
    ("celular", "utero"): "classificacao/classificacaoCelularUtero.py",
    ("celular", "mama"): "classificacao/classificacaoCelularMama.py",
    ("celular", "pulmao"): "classificacao/classificacaoCelularPulmao.py",
    ("celular", "colon"): "classificacao/classificacaoCelularColon.py",
    ("celular", "oral"): "classificacao/classificacaoCelularOral.py",
    ("celular", "cervical"): "classificacao/classificacaoCelularCervical.py",
    ("celular", "rim"): "classificacao/classificacaoCelularRim.py",
    ("tomografia", "rim"): "classificacao/classificacaoTomografiaRim.py",
    ("tomografia", "cerebro"): "classificacao/classificacaoTomografiaCerebro.py",
    ("tomografia", "abdomen"): "classificacao/classificacaoTomografiaAbdomen.py",
    ("fotografia", "pele"): "classificacao/classificacaoFotografiaPele.py",
}


def resolve_script(exam_type: str, location: str) -> str | None:
    return ROUTE_MAP.get((exam_type, location)) or ROUTE_MAP.get((exam_type, "*"))


@app.get("/health")
def health():
    return {"status": "ok"}


@app.post("/v1/classify")
def classify(payload: ClassifyRequest):
    script = resolve_script(payload.examType, payload.location)
    if not script:
        raise HTTPException(status_code=400, detail="Tipo/localizacao nao suportados")

    script_path = os.path.join("/app/public", script)
    if not os.path.exists(script_path):
        raise HTTPException(status_code=500, detail=f"Script nao encontrado: {script_path}")

    command = ["python3", script_path, payload.filePath]
    result = subprocess.run(command, capture_output=True, text=True, timeout=600)

    if result.returncode != 0:
        raise HTTPException(status_code=500, detail=result.stderr.strip() or "Erro na classificacao")

    return {
        "status": "ok",
        "requestId": payload.requestId,
        "prediction": result.stdout.strip(),
        "error": None,
    }
