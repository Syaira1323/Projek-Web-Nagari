import sys
import json
from fastapi.testclient import TestClient
from main import app

# Ensure utf-8 encoding on Windows console
if sys.platform == "win32":
    sys.stdout.reconfigure(encoding='utf-8')

print("=" * 80)
print("PENGUJIAN SUITE ENDPOINT FASTAPI INFERENCE DISTILBERT")
print("=" * 80)

client = TestClient(app)

# Test 1: GET /health
print("\n[TEST 1] Pengujian GET /health ...")
with client:
    res_health = client.get("/health")
    print(f"  Status Code: {res_health.status_code}")
    print(f"  Response   : {res_health.json()}")
    assert res_health.status_code == 200, "Health check failed!"
    assert res_health.json()["status"] == "ok", "Status must be ok"
    assert res_health.json()["classes"] == 7, "Classes count must be 7"
    print("  => [PASS] Health check berhasil!")

    # Test 2: Pengujian 7 Kalimat Warga Realistis (1 per kelas)
    test_prompts = [
        {
            "target": "SKD (suket_domisili)",
            "text": "Saya membutuhkan surat keterangan bahwa saya tinggal di wilayah Nagari Koto Malintang."
        },
        {
            "target": "SKU (suket_usaha)",
            "text": "Saya perlu surat untuk membuktikan bahwa saya mempunyai usaha warung kelontong."
        },
        {
            "target": "SKTM (suket_tidak_mampu)",
            "text": "Anak saya membutuhkan surat keterangan tidak mampu untuk persyaratan beasiswa."
        },
        {
            "target": "SKP (sket_penghasilan)",
            "text": "Saya memerlukan surat keterangan penghasilan untuk melengkapi pengajuan kredit."
        },
        {
            "target": "SKBM (suket_belum_menikah)",
            "text": "Saya membutuhkan surat keterangan belum menikah untuk persyaratan pekerjaan."
        },
        {
            "target": "SKM (suket_meninggal)",
            "text": "Orang tua saya meninggal dan saya memerlukan surat keterangan kematian."
        },
        {
            "target": "Model NA (surat_nikah_na)",
            "text": "Saya membutuhkan surat pengantar untuk mengurus pendaftaran pernikahan di KUA."
        }
    ]

    print("\n[TEST 2] Pengujian POST /predict pada 7 Jenis Surat ...\n")

    predict_results = []

    for i, test in enumerate(test_prompts, 1):
        payload = {"text": test["text"]}
        res = client.post("/predict", json=payload)

        assert res.status_code == 200, f"Predict failed for test {i}!"
        data = res.json()

        assert data["success"] is True
        assert len(data["recommendations"]) == 3

        recs = data["recommendations"]
        # Validasi Descending Confidence
        assert recs[0]["confidence"] >= recs[1]["confidence"] >= recs[2]["confidence"], "Confidence ranking must be descending!"
        # Validasi range 0 - 1
        for r in recs:
            assert 0.0 <= r["confidence"] <= 1.0, f"Confidence {r['confidence']} out of bounds!"

        predict_results.append({
            "no": i,
            "target": test["target"],
            "input": test["text"],
            "top1": recs[0],
            "top2": recs[1],
            "top3": recs[2],
            "raw_response": data
        })

        print(f"Kasus {i} [Target: {test['target']}]")
        print(f"  Input       : \"{test['text']}\"")
        print(f"  Top 1 (R1)  : [{recs[0]['code']}] {recs[0]['label']} | Conf: {recs[0]['confidence']*100:.2f}%")
        print(f"  Top 2 (R2)  : [{recs[1]['code']}] {recs[1]['label']} | Conf: {recs[1]['confidence']*100:.2f}%")
        print(f"  Top 3 (R3)  : [{recs[2]['code']}] {recs[2]['label']} | Conf: {recs[2]['confidence']*100:.2f}%\n")

    # Test 3: Pengujian Validasi Input Kosong & Whitespace
    print("[TEST 3] Pengujian Validasi Input Kosong (HTTP 422) ...")

    # Empty string
    res_empty = client.post("/predict", json={"text": ""})
    print(f"  Empty text \"\" -> Status: {res_empty.status_code}, Detail: {res_empty.json()}")
    assert res_empty.status_code == 422, "Empty string should return HTTP 422"

    # Whitespace only
    res_ws = client.post("/predict", json={"text": "      "})
    print(f"  Whitespace \"   \" -> Status: {res_ws.status_code}, Detail: {res_ws.json()}")
    assert res_ws.status_code == 422, "Whitespace string should return HTTP 422"
    print("  => [PASS] Seluruh pengujian validasi input kosong berhasil ditolak dengan HTTP 422!\n")

print("=" * 80)
print("SELURUH TEST SUITE FASTAPI INFERENCE DISTILBERT SUKSES 100%!")
print("=" * 80)
