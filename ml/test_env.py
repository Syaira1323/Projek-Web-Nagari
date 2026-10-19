import sys
print("Python version:", sys.version)

try:
    import torch
    print("Torch version:", torch.__version__)
    print("CUDA available:", torch.cuda.is_available())
except Exception as e:
    print("Torch error:", e)

try:
    import transformers
    print("Transformers version:", transformers.__version__)
except Exception as e:
    print("Transformers error:", e)

try:
    import sklearn
    print("Sklearn version:", sklearn.__version__)
except Exception as e:
    print("Sklearn error:", e)

try:
    import pandas as pd
    print("Pandas version:", pd.__version__)
except Exception as e:
    print("Pandas error:", e)

try:
    import datasets
    print("Datasets version:", datasets.__version__)
except Exception as e:
    print("Datasets error:", e)
