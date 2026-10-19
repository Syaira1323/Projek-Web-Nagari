import os
import sys
import json
import torch
import numpy as np
from transformers import AutoTokenizer, AutoModelForSequenceClassification

# Ensure utf-8 encoding on Windows console
if sys.platform == "win32":
    sys.stdout.reconfigure(encoding='utf-8')

MODEL_DIR = r"ml\models\distilbert-surat-nagari"
CONFIG_PATH = os.path.join(MODEL_DIR, "config.json")

print("=" * 80)
print("VALIDASI TAMBAHAN MODEL DISTILBERT - LAYANAN SURAT NAGARI KOTO MALINTANG")
print("=" * 80)

# 1. Konfirmasi Mapping Label dari Config
if not os.path.exists(CONFIG_PATH):
    raise FileNotFoundError(f"Config file not found at: {CONFIG_PATH}")

with open(CONFIG_PATH, "r", encoding="utf-8") as f:
    config_data = json.load(f)

id2label = config_data.get("id2label", {})
label2id = config_data.get("label2id", {})

print("\n[1] KONFIRMASI MAPPING 7 LABEL MODEL:")
print(f"  id2label : {id2label}")
print(f"  label2id : {label2id}")

# 2. Muat Tokenizer dan Model
print(f"\n[2] Memuat Model & Tokenizer dari: {MODEL_DIR}...")
tokenizer = AutoTokenizer.from_pretrained(MODEL_DIR)
model = AutoModelForSequenceClassification.from_pretrained(MODEL_DIR)
model.eval()

device = "cuda" if torch.cuda.is_available() else "cpu"
model.to(device)

# 3. 14 Kalimat Uji Baru (2 kalimat per kelas)
test_cases = [
    # 1. Model NA (Pengantar Nikah)
    {
        "expected": "Model NA",
        "input": "Pak kami mau melangsungkan akad nikah dengan orang Maninjau bulan depan, mohon dibuatkan surat pengantar untuk dibawa ke kantor KUA."
    },
    {
        "expected": "Model NA",
        "input": "Butuh kelengkapan berkas untuk mendaftar perkawinan anak gadis saya di KUA Tanjung Raya."
    },
    # 2. SKU (Keterangan Usaha)
    {
        "expected": "SKU",
        "input": "Saya mau mengajukan tambahan modal usaha bengkel motor saya ke Bank BRI, pihak bank minta surat keterangan bahwa saya benar punya usaha aktif di jorong ini."
    },
    {
        "expected": "SKU",
        "input": "Minta surat keterangan usaha ternak ayam potong untuk syarat pengajuan bantuan permodalan dari dinas koperasi."
    },
    # 3. SKM (Keterangan Kematian)
    {
        "expected": "SKM",
        "input": "Mohon bantuannya untuk pembuatan surat keterangan meninggal dunia bagi orang tua kami yang wafat dua hari lalu guna pengurusan akta kematian dan taspen."
    },
    {
        "expected": "SKM",
        "input": "Perlu surat keterangan wafat untuk almarhum suami saya demi keperluan pengalihan rekening tabungan dan asuransi jiwa."
    },
    # 4. SKBM (Keterangan Belum Menikah)
    {
        "expected": "SKBM",
        "input": "Saya sedang proses seleksi masuk anggota kepolisian dan diminta menyerahkan surat bukti resmi belum pernah berstatus kawin atau menikah."
    },
    {
        "expected": "SKBM",
        "input": "Mohon surat pernyataan dari nagari yang menerangkan status saya masih lajang belum berkeluarga untuk berkas lamaran pegawai BUMN."
    },
    # 5. SKP (Keterangan Penghasilan)
    {
        "expected": "SKP",
        "input": "Pihak kampus anak saya meminta surat keterangan rincian penghasilan kotor orang tua per bulan sebagai petani karena kami tidak punya slip gaji formal."
    },
    {
        "expected": "SKP",
        "input": "Saya butuh surat keterangan besaran pendapatan rata-rata bulanan dari nagari untuk lampiran permohonan cicilan KPR rumah subsidi."
    },
    # 6. SKTM (Keterangan Tidak Mampu)
    {
        "expected": "SKTM",
        "input": "Anak saya masuk rumah sakit tapi kami dari keluarga prasejahtera dan kesulitan biaya berobat, butuh surat keterangan tidak mampu untuk keringanan dan pengurusan BPJS PBI."
    },
    {
        "expected": "SKTM",
        "input": "Tolong buatkan surat keterangan miskin/kurang mampu untuk syarat permohonan beasiswa Program Indonesia Pintar (PIP) anak saya di sekolah."
    },
    # 7. SKD (Keterangan Domisili)
    {
        "expected": "SKD",
        "input": "Saya baru ngontrak rumah di Jorong Muko-Muko Nagari Koto Malintang dan butuh surat keterangan domisili tempat tinggal sementara untuk keperluan kantor pos dan lingkungan."
    },
    {
        "expected": "SKD",
        "input": "Minta surat keterangan bahwa saya benar-benar bertempat tinggal dan menetap di wilayah Nagari Koto Malintang untuk keperluan pembuatan rekening bank baru."
    }
]

print(f"\n[3] MENJALANKAN INFERENCE PADA {len(test_cases)} KALIMAT UJI BARU...\n")

results = []
class_summary = {
    "Model NA": {"total": 0, "correct": 0},
    "SKU": {"total": 0, "correct": 0},
    "SKM": {"total": 0, "correct": 0},
    "SKBM": {"total": 0, "correct": 0},
    "SKP": {"total": 0, "correct": 0},
    "SKTM": {"total": 0, "correct": 0},
    "SKD": {"total": 0, "correct": 0}
}

for idx, item in enumerate(test_cases, 1):
    text = item["input"]
    expected = item["expected"]

    inputs = tokenizer(text, return_tensors="pt", truncation=True, max_length=64, padding=True).to(device)
    with torch.no_grad():
        outputs = model(**inputs)
        logits = outputs.logits
        probs = torch.softmax(logits, dim=-1).squeeze().cpu().numpy()

    top_indices = np.argsort(probs)[::-1][:3]
    top1_label = id2label[str(top_indices[0])]
    top1_prob = float(probs[top_indices[0]])
    top2_label = id2label[str(top_indices[1])]
    top2_prob = float(probs[top_indices[1]])
    top3_label = id2label[str(top_indices[2])]
    top3_prob = float(probs[top_indices[2]])

    is_correct = (top1_label == expected)
    class_summary[expected]["total"] += 1
    if is_correct:
        class_summary[expected]["correct"] += 1

    res_entry = {
        "no": idx,
        "input": text,
        "expected": expected,
        "top1": top1_label,
        "conf1": top1_prob,
        "top2": top2_label,
        "conf2": top2_prob,
        "top3": top3_label,
        "conf3": top3_prob,
        "is_correct": is_correct
    }
    results.append(res_entry)

    status_sym = "[BENAR]" if is_correct else "[SALAH]"
    print(f"No. {idx} [{expected}] -> Prediksi: {top1_label} ({top1_prob*100:.2f}%) {status_sym}")
    print(f"   Input : \"{text}\"")
    print(f"   Top 1 : {top1_label:<10} | Confidence: {top1_prob*100:6.2f}%")
    print(f"   Top 2 : {top2_label:<10} | Confidence: {top2_prob*100:6.2f}%")
    print(f"   Top 3 : {top3_label:<10} | Confidence: {top3_prob*100:6.2f}%\n")

total_tests = len(test_cases)
correct_tests = sum(1 for r in results if r["is_correct"])
wrong_tests = total_tests - correct_tests
acc = (correct_tests / total_tests) * 100

print("=" * 80)
print("RINGKASAN AKURASI PER KELAS:")
print("=" * 80)
print(f"{'Kelas':<12} | {'Jumlah Pengujian':<16} | {'Top-1 Benar':<12} | {'Akurasi':<10}")
print("-" * 60)
for k, v in class_summary.items():
    k_acc = (v["correct"] / v["total"]) * 100 if v["total"] > 0 else 0
    print(f"{k:<12} | {v['total']:<16} | {v['correct']:<12} | {k_acc:6.2f}%")

print("-" * 60)
print(f"Total pengujian          : {total_tests}")
print(f"Jumlah prediksi benar    : {correct_tests}")
print(f"Jumlah prediksi salah    : {wrong_tests}")
print(f"Akurasi pengujian tambahan: {acc:.2f}%\n")

# Simpan hasil pengujian ke JSON
out_json_path = r"ml\evaluation\additional_validation_results.json"
with open(out_json_path, "w", encoding="utf-8") as f:
    json.dump({
        "summary": {
            "total": total_tests,
            "correct": correct_tests,
            "wrong": wrong_tests,
            "accuracy": acc
        },
        "class_summary": class_summary,
        "results": results
    }, f, indent=4)

print(f"Hasil validasi lengkap disimpan di: {out_json_path}")
