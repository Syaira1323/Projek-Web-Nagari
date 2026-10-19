import os
import json
from contextlib import asynccontextmanager
from typing import List, Dict, Any

import torch
import numpy as np
from fastapi import FastAPI, HTTPException, status
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel, Field
from transformers import AutoTokenizer, AutoModelForSequenceClassification

# Base Path & Model Configuration
BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
MODEL_DIR = os.path.join(BASE_DIR, "models", "distilbert-surat-nagari")
CONFIG_PATH = os.path.join(MODEL_DIR, "config.json")

# Metadata Mapping untuk 7 Jenis Surat Nagari Koto Malintang
SURAT_METADATA: Dict[str, Dict[str, str]] = {
    "Model NA": {
        "code": "surat_nikah_na",
        "label": "Surat Pengantar Nikah (Model NA)"
    },
    "SKU": {
        "code": "suket_usaha",
        "label": "Surat Keterangan Usaha (SKU)"
    },
    "SKM": {
        "code": "suket_meninggal",
        "label": "Surat Keterangan Kematian (SKM)"
    },
    "SKBM": {
        "code": "suket_belum_menikah",
        "label": "Surat Keterangan Belum Menikah (SKBM)"
    },
    "SKP": {
        "code": "sket_penghasilan",
        "label": "Surat Keterangan Penghasilan (SKP)"
    },
    "SKTM": {
        "code": "suket_tidak_mampu",
        "label": "Surat Keterangan Tidak Mampu (SKTM)"
    },
    "SKD": {
        "code": "suket_domisili",
        "label": "Surat Keterangan Domisili (SKD)"
    }
}

# Global Model Container
model_context: Dict[str, Any] = {}

@asynccontextmanager
async def lifespan(app: FastAPI):
    """
    Lifecycle event: Memuat model dan tokenizer satu kali saat microservice startup.
    """
    print("=" * 60)
    print("MEMULAI MICROSERVICE INFERENCE DISTILBERT NAGARI KOTO MALINTANG")
    print(f"Memuat model dari: {MODEL_DIR}")

    if not os.path.exists(MODEL_DIR):
        raise RuntimeError(f"Folder model tidak ditemukan di: {MODEL_DIR}")

    # Memuat id2label dari config.json
    with open(CONFIG_PATH, "r", encoding="utf-8") as f:
        config_json = json.load(f)
    id2label = config_json.get("id2label", {})

    tokenizer = AutoTokenizer.from_pretrained(MODEL_DIR)
    model = AutoModelForSequenceClassification.from_pretrained(MODEL_DIR)
    model.eval()

    device = "cuda" if torch.cuda.is_available() else "cpu"
    model.to(device)

    model_context["tokenizer"] = tokenizer
    model_context["model"] = model
    model_context["device"] = device
    model_context["id2label"] = id2label
    model_context["classes_count"] = len(id2label)

    print(f"Model berhasil dimuat pada device: {device} dengan {len(id2label)} kelas.")
    print("=" * 60)

    yield

    # Cleanup saat shutdown
    model_context.clear()
    print("Microservice stopped.")

# Inisialisasi FastAPI
app = FastAPI(
    title="Layanan Surat Nagari Koto Malintang - DistilBERT Inference API",
    description="Microservice cerdas klasifikasi teks permohonan surat warga berbasis fine-tuned DistilBERT (cahya/distilbert-base-indonesian).",
    version="1.0.0",
    lifespan=lifespan
)

# CORS Middleware (mengizinkan koneksi dari Laravel Backend & Frontend)
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Schema Request & Response
class PredictRequest(BaseModel):
    text: str = Field(
        ...,
        description="Teks kebutuhan atau maksud permohonan surat yang ditulis oleh warga.",
        example="Saya membutuhkan surat untuk mengajukan beasiswa karena kondisi ekonomi keluarga saya kurang mampu."
    )

class RecommendationItem(BaseModel):
    rank: int = Field(..., description="Peringkat rekomendasi (1-3)")
    code: str = Field(..., description="Kode jenis surat baku")
    label: str = Field(..., description="Nama resmi surat Nagari Koto Malintang")
    confidence: float = Field(..., description="Probabilitas desimal hasil Softmax output model (0.0 - 1.0)")

class PredictResponse(BaseModel):
    success: bool = True
    input: str
    recommendations: List[RecommendationItem]

class HealthResponse(BaseModel):
    status: str
    model: str
    classes: int

@app.get(
    "/health",
    response_model=HealthResponse,
    summary="Health Check Microservice",
    tags=["Monitoring"]
)
def health_check():
    """
    Endpoint pemantauan status microservice dan kesiapan model.
    """
    if "model" not in model_context:
        raise HTTPException(
            status_code=status.HTTP_503_SERVICE_UNAVAILABLE,
            detail="Model belum siap dimuat."
        )
    return {
        "status": "ok",
        "model": "distilbert-surat-nagari",
        "classes": model_context.get("classes_count", 7)
    }

@app.post(
    "/predict",
    response_model=PredictResponse,
    summary="Klasifikasi Teks Permohonan Warga (Top-3 Rekomendasi)",
    tags=["Inference"]
)
def predict_surat(request: PredictRequest):
    """
    Menerima teks permohonan warga, melakukan tokenisasi, forward pass DistilBERT,
    kalkulasi probabilitas Softmax, dan mengembalikan Top-3 rekomendasi jenis surat.
    """
    raw_text = request.text
    if not raw_text or not raw_text.strip():
        raise HTTPException(
            status_code=status.HTTP_422_UNPROCESSABLE_ENTITY,
            detail="Teks permohonan warga tidak boleh kosong atau hanya berupa spasi."
        )

    cleaned_text = raw_text.strip()
    if len(cleaned_text) > 1000:
        raise HTTPException(
            status_code=status.HTTP_422_UNPROCESSABLE_ENTITY,
            detail="Panjang teks melebihi batas maksimal 1000 karakter."
        )

    tokenizer = model_context["tokenizer"]
    model = model_context["model"]
    device = model_context["device"]
    id2label = model_context["id2label"]

    # Tokenisasi
    inputs = tokenizer(
        cleaned_text,
        return_tensors="pt",
        truncation=True,
        max_length=64,
        padding=True
    ).to(device)

    # Inference (No Gradient)
    with torch.no_grad():
        outputs = model(**inputs)
        logits = outputs.logits
        # Softmax Probability Distribution
        probs = torch.softmax(logits, dim=-1).squeeze().cpu().numpy()

    # Ranking Top-3 Probabilitas Tertinggi (Descending)
    top_indices = np.argsort(probs)[::-1][:3]

    recommendations: List[RecommendationItem] = []
    for rank_idx, class_idx in enumerate(top_indices, 1):
        class_name = id2label[str(class_idx)]
        conf_val = float(probs[class_idx])

        meta = SURAT_METADATA.get(class_name, {
            "code": class_name.lower().replace(" ", "_"),
            "label": class_name
        })

        recommendations.append(
            RecommendationItem(
                rank=rank_idx,
                code=meta["code"],
                label=meta["label"],
                confidence=round(conf_val, 4)
            )
        )

    return PredictResponse(
        success=True,
        input=cleaned_text,
        recommendations=recommendations
    )
