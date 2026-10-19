import os
import sys
import json
import shutil
import numpy as np
import pandas as pd
import torch
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score, precision_recall_fscore_support, classification_report, confusion_matrix
import matplotlib.pyplot as plt
import seaborn as sns
from transformers import (
    AutoTokenizer,
    AutoModelForSequenceClassification,
    Trainer,
    TrainingArguments,
    set_seed
)
from datasets import Dataset

# Set optimal CPU threading
cpu_count = os.cpu_count() or 4
torch.set_num_threads(cpu_count)
print(f"PyTorch configured with {cpu_count} CPU threads", flush=True)

# Set seed for reproducibility
SEED = 42
set_seed(SEED)

print("=" * 60, flush=True)
print("FINE-TUNING DISTILBERT FOR NAGARI KOTO MALINTANG", flush=True)
print("=" * 60, flush=True)

# 1. Paths and Config
RAW_DATASET_PATH = r"C:\Users\Lenovo\Downloads\dataset_surat_warga.csv"
DATASET_DIR = r"ml\dataset"
OUTPUT_MODEL_DIR = r"ml\models\distilbert-surat-nagari"
EVAL_DIR = r"ml\evaluation"

os.makedirs(DATASET_DIR, exist_ok=True)
os.makedirs(OUTPUT_MODEL_DIR, exist_ok=True)
os.makedirs(EVAL_DIR, exist_ok=True)

# Copy dataset to ml/dataset/
LOCAL_DATASET_PATH = os.path.join(DATASET_DIR, "dataset_surat_warga.csv")
if os.path.exists(RAW_DATASET_PATH):
    shutil.copyfile(RAW_DATASET_PATH, LOCAL_DATASET_PATH)
    print(f"[1] Dataset copied from {RAW_DATASET_PATH} to {LOCAL_DATASET_PATH}", flush=True)
else:
    print(f"[1] Using existing dataset at {LOCAL_DATASET_PATH}", flush=True)

# 2. Load & Validate Dataset
df = pd.read_csv(LOCAL_DATASET_PATH)
print(f"Total rows: {len(df)}, Columns: {list(df.columns)}", flush=True)

# Identify column names
text_col = [c for c in df.columns if 'teks' in c.lower() or 'text' in c.lower()][0]
label_col = [c for c in df.columns if 'label' in c.lower() or 'kategori' in c.lower() or 'surat' in c.lower()][0]
print(f"Text column: '{text_col}', Label column: '{label_col}'", flush=True)

# Clean text and label whitespace
df[text_col] = df[text_col].astype(str).str.strip()
df[label_col] = df[label_col].astype(str).str.strip()

TARGET_LABELS = ['Model NA', 'SKU', 'SKM', 'SKBM', 'SKP', 'SKTM', 'SKD']
print(f"Target classes ({len(TARGET_LABELS)}): {TARGET_LABELS}", flush=True)

# Class distribution
dist = df[label_col].value_counts().to_dict()
print(f"Class Distribution: {dist}", flush=True)

# Mapping
label2id = {label: i for i, label in enumerate(TARGET_LABELS)}
id2label = {i: label for i, label in enumerate(TARGET_LABELS)}

df['label_id'] = df[label_col].map(label2id)

# Check for unmapped labels
if df['label_id'].isnull().any():
    unmapped = df[df['label_id'].isnull()][label_col].unique()
    raise ValueError(f"Found unmapped labels: {unmapped}")

# 3. Stratified Split (80% Train, 10% Validation, 10% Test)
train_df, temp_df = train_test_split(
    df,
    test_size=0.20,
    stratify=df['label_id'],
    random_state=SEED
)

val_df, test_df = train_test_split(
    temp_df,
    test_size=0.50,
    stratify=temp_df['label_id'],
    random_state=SEED
)

print(f"\n[2] Dataset Split:", flush=True)
print(f"  - Training   : {len(train_df)} samples ({len(train_df)/len(df)*100:.1f}%)", flush=True)
print(f"  - Validation : {len(val_df)} samples ({len(val_df)/len(df)*100:.1f}%)", flush=True)
print(f"  - Testing    : {len(test_df)} samples ({len(test_df)/len(df)*100:.1f}%)", flush=True)

# Save split files
train_df.to_csv(os.path.join(DATASET_DIR, "train.csv"), index=False)
val_df.to_csv(os.path.join(DATASET_DIR, "val.csv"), index=False)
test_df.to_csv(os.path.join(DATASET_DIR, "test.csv"), index=False)
print("  Split CSV files saved to ml/dataset/", flush=True)

# 4. Tokenizer & Tokenization
MODEL_NAME = "cahya/distilbert-base-indonesian"
MAX_LENGTH = 64
print(f"\n[3] Loading Base Model & Tokenizer: {MODEL_NAME} (Max Length: {MAX_LENGTH})...", flush=True)

tokenizer = AutoTokenizer.from_pretrained(MODEL_NAME)

def tokenize_function(examples):
    return tokenizer(examples[text_col], padding="max_length", truncation=True, max_length=MAX_LENGTH)

train_dataset = Dataset.from_pandas(train_df[[text_col, 'label_id']]).rename_column('label_id', 'label')
val_dataset = Dataset.from_pandas(val_df[[text_col, 'label_id']]).rename_column('label_id', 'label')
test_dataset = Dataset.from_pandas(test_df[[text_col, 'label_id']]).rename_column('label_id', 'label')

train_tokenized = train_dataset.map(tokenize_function, batched=True)
val_tokenized = val_dataset.map(tokenize_function, batched=True)
test_tokenized = test_dataset.map(tokenize_function, batched=True)

# 5. Model Setup
model = AutoModelForSequenceClassification.from_pretrained(
    MODEL_NAME,
    num_labels=len(TARGET_LABELS),
    id2label=id2label,
    label2id=label2id
)

device = "cuda" if torch.cuda.is_available() else "cpu"
print(f"Using device: {device}", flush=True)

def compute_metrics(pred):
    labels = pred.label_ids
    logits = pred.predictions[0] if isinstance(pred.predictions, tuple) else pred.predictions
    preds = np.argmax(logits, axis=-1)
    acc = accuracy_score(labels, preds)
    precision, recall, f1, _ = precision_recall_fscore_support(labels, preds, average='macro', zero_division=0)
    w_precision, w_recall, w_f1, _ = precision_recall_fscore_support(labels, preds, average='weighted', zero_division=0)
    return {
        'accuracy': acc,
        'f1_macro': f1,
        'precision_macro': precision,
        'recall_macro': recall,
        'f1_weighted': w_f1
    }

# 6. Training Arguments
EPOCHS = 3
BATCH_SIZE = 16

training_args = TrainingArguments(
    output_dir=r"ml\training_checkpoints",
    num_train_epochs=EPOCHS,
    per_device_train_batch_size=BATCH_SIZE,
    per_device_eval_batch_size=32,
    warmup_steps=30,
    weight_decay=0.01,
    learning_rate=3e-5,
    eval_strategy="epoch",
    save_strategy="epoch",
    load_best_model_at_end=True,
    metric_for_best_model="f1_macro",
    greater_is_better=True,
    logging_steps=15,
    seed=SEED,
    report_to="none"
)

trainer = Trainer(
    model=model,
    args=training_args,
    train_dataset=train_tokenized,
    eval_dataset=val_tokenized,
    compute_metrics=compute_metrics,
)

print(f"\n[4] Starting Fine-Tuning DistilBERT ({EPOCHS} Epochs, {len(train_tokenized)//BATCH_SIZE * EPOCHS} total steps)...", flush=True)
trainer.train()
print("\n[5] Fine-Tuning Completed Successfully!", flush=True)

# 7. Final Evaluation on Test Set
print("\n" + "=" * 60, flush=True)
print("EVALUATION ON TEST SET (210 unseen samples)", flush=True)
print("=" * 60, flush=True)

test_predictions = trainer.predict(test_tokenized)
y_true = test_predictions.label_ids
test_logits = test_predictions.predictions[0] if isinstance(test_predictions.predictions, tuple) else test_predictions.predictions
y_pred = np.argmax(test_logits, axis=-1)
y_probs = torch.softmax(torch.tensor(test_logits), dim=-1).numpy()

acc = accuracy_score(y_true, y_pred)
macro_p, macro_r, macro_f1, _ = precision_recall_fscore_support(y_true, y_pred, average='macro', zero_division=0)
weight_p, weight_r, weight_f1, _ = precision_recall_fscore_support(y_true, y_pred, average='weighted', zero_division=0)

eval_summary = {
    "accuracy": float(acc),
    "macro_precision": float(macro_p),
    "macro_recall": float(macro_r),
    "macro_f1": float(macro_f1),
    "weighted_f1": float(weight_f1)
}

print(f"Accuracy        : {acc:.4f} ({acc*100:.2f}%)", flush=True)
print(f"Macro Precision : {macro_p:.4f}", flush=True)
print(f"Macro Recall    : {macro_r:.4f}", flush=True)
print(f"Macro F1        : {macro_f1:.4f}", flush=True)
print(f"Weighted F1     : {weight_f1:.4f}", flush=True)

# Classification Report
clf_report_dict = classification_report(y_true, y_pred, target_names=TARGET_LABELS, output_dict=True, zero_division=0)
clf_report_text = classification_report(y_true, y_pred, target_names=TARGET_LABELS, digits=4, zero_division=0)

print("\nClassification Report:", flush=True)
print(clf_report_text, flush=True)

# Confusion Matrix
cm = confusion_matrix(y_true, y_pred)
print("\nConfusion Matrix:", flush=True)
print(cm, flush=True)

# Plot & Save Confusion Matrix
plt.figure(figsize=(9, 7))
sns.heatmap(cm, annot=True, fmt='d', cmap='Blues', xticklabels=TARGET_LABELS, yticklabels=TARGET_LABELS)
plt.title('Confusion Matrix - DistilBERT Surat Nagari (Test Set)')
plt.ylabel('Actual Label')
plt.xlabel('Predicted Label')
plt.tight_layout()
cm_image_path = os.path.join(EVAL_DIR, "confusion_matrix.png")
plt.savefig(cm_image_path, dpi=300)
plt.close()
print(f"Confusion matrix plot saved to {cm_image_path}", flush=True)

# Save Evaluation Metrics JSON
eval_export = {
    "metrics": eval_summary,
    "classification_report": clf_report_dict,
    "confusion_matrix": cm.tolist(),
    "labels": TARGET_LABELS,
    "dataset_split": {
        "total": len(df),
        "train": len(train_df),
        "val": len(val_df),
        "test": len(test_df)
    },
    "hyperparameters": {
        "base_model": MODEL_NAME,
        "max_length": MAX_LENGTH,
        "epochs": EPOCHS,
        "learning_rate": 3e-5,
        "batch_size": BATCH_SIZE,
        "seed": SEED
    }
}

with open(os.path.join(EVAL_DIR, "eval_results.json"), "w", encoding="utf-8") as f:
    json.dump(eval_export, f, indent=4)

# 8. Save Final Model and Tokenizer
print(f"\n[6] Saving Final Model & Tokenizer to {OUTPUT_MODEL_DIR}...", flush=True)
trainer.save_model(OUTPUT_MODEL_DIR)
tokenizer.save_pretrained(OUTPUT_MODEL_DIR)
print(f"Model saved successfully in: {OUTPUT_MODEL_DIR}", flush=True)

# 9. Top-3 Inference Test on Fresh Examples
print("\n" + "=" * 60, flush=True)
print("TOP-3 INFERENCE DEMONSTRATION ON SAMPLE CITIZEN INPUTS", flush=True)
print("=" * 60, flush=True)

sample_inputs = [
    "Saya membutuhkan surat keterangan untuk pengajuan beasiswa anak kuliah karena penghasilan bulanan keluarga tidak mencukupi.",
    "Perlu surat untuk mengurus izin mendirikan kios kelontong dan permohonan pinjaman KUR di bank Nagari.",
    "Surat keterangan untuk menyatakan bahwa saya saat ini berdomisili menetap di Jorong Pasar Nagari Koto Malintang.",
    "Pengurusan surat pengantar untuk kelengkapan administrasi pendaftaran pernikahan di KUA bulan depan.",
    "Surat keterangan bahwa sampai saat ini saya belum pernah menikah untuk syarat melamar pekerjaan.",
    "Saya butuh surat pindah domisili kependudukan ke Kota Padang karena mutasi tempat kerja.",
    "Permohonan surat keterangan kematian almarhum kakek saya untuk keperluan klaim asuransi dan waris."
]

model.eval()
inference_results = []

for text in sample_inputs:
    inputs = tokenizer(text, return_tensors="pt", truncation=True, max_length=MAX_LENGTH, padding=True).to(device)
    with torch.no_grad():
        outputs = model(**inputs)
        probs = torch.softmax(outputs.logits, dim=-1).squeeze().cpu().numpy()

    top3_indices = np.argsort(probs)[::-1][:3]
    top3 = [
        {"label": TARGET_LABELS[idx], "prob": float(probs[idx])}
        for idx in top3_indices
    ]
    inference_results.append({
        "input_text": text,
        "top3": top3
    })

    print(f"\nInput: \"{text}\"", flush=True)
    for rank, pred in enumerate(top3, 1):
        print(f"  {rank}. {pred['label']:<10} : {pred['prob']*100:6.2f}%", flush=True)

with open(os.path.join(EVAL_DIR, "sample_inferences.json"), "w", encoding="utf-8") as f:
    json.dump(inference_results, f, indent=4)

print("\n" + "=" * 60, flush=True)
print("ALL TRAINING & EVALUATION TASKS COMPLETED SUCCESSFULLY!", flush=True)
print("=" * 60, flush=True)
