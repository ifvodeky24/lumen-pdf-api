# convert_xlsx.py
import sys
import pdfplumber
import pandas as pd

pdf_file = sys.argv[1]
output_file = sys.argv[2]

with pdfplumber.open(pdf_file) as pdf:
    all_tables = []
    for page in pdf.pages:
        tables = page.extract_tables()
        for table in tables:
            df = pd.DataFrame(table)
            all_tables.append(df)

if not all_tables:
    print("No tables found")
    exit(1)

with pd.ExcelWriter(output_file, engine='openpyxl') as writer:
    for i, df in enumerate(all_tables):
        df.to_excel(writer, sheet_name=f'Sheet{i+1}', index=False)

print("Conversion successful")
