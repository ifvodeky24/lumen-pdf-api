
# Lumen PDF API

A lightweight and self-hosted REST API built with **Lumen**, providing PDF compression and file conversion features:

- **Compress** PDF files to reduce size
- **Convert** PDF → DOCX / XLSX / PPTX (via Python scripts)
- **Convert** PDF → PNG images + ZIP archive
- **No manual file paths**—fully automated and OS-independent

---

## 🚀 Features

| Endpoint                | Description                                             |
|------------------------|---------------------------------------------------------|
| `POST /compress`       | Compress a PDF using Ghostscript – returns reduced-size PDF |
| `POST /convert/pdf-to-docx` | Convert PDF → Word `.docx` format using `pdf2docx` Python |
| `POST /convert/pdf-to-xlsx` | Convert PDF → Excel `.xlsx` (table extraction) via Python |
| `POST /convert/pdf-to-pptx` | Convert PDF pages to images and compile into `.pptx` using Python |

Each endpoint accepts a multipart-form upload (`file: PDF`) and returns a **public URL** for the resulting file.

---

## ⚙️ Requirements

Install the following system dependencies:

**macOS (via Homebrew):**
```bash
brew install ghostscript imagemagick poppler
pip3 install pdf2docx pdfplumber pandas openpyxl pdf2image python-pptx
```

**Linux (Debian/Ubuntu):**
```bash
sudo apt update
sudo apt install ghostscript imagemagick poppler-utils python3-pip
pip3 install pdf2docx pdfplumber pandas openpyxl pdf2image python-pptx
```

*(Windows users: install Poppler and Python libs manually. No need for LibreOffice.)*

---

## 🛠️ Installation

1. Clone the repo:
    ```bash
    git clone https://github.com/ifvodeky24/lumen-pdf-api.git
    cd lumen-pdf-api
    composer install
    ```
2. Set up storage directories:
    ```bash
    mkdir -p storage/app/{temp,public/converted}
    ln -s $(pwd)/storage/app/public public/storage
    chmod -R 775 storage
    ```
3. Start the server (use any port):
    ```bash
    php -S localhost:8000 -t public
    ```

---

## 📬 API Usage

Send `POST` requests via **Postman**, **curl**, or **Fetch** with:

- `file`: PDF file to process

### 1. Compress PDF
```
POST /compress
```
**Response:**
```json
{
  "message": "PDF compressed successfully",
  "url": "http://…/storage/converted/compressed_… .pdf"
}
```

### 2. PDF → Word
```
POST /convert/pdf-to-docx
```
**Response:**
```json
{
  "message": "PDF successfully converted to DOCX",
  "url": "http://…/storage/converted/… .docx"
}
```

### 3. PDF → Excel
```
POST /convert/pdf-to-xlsx
```
**Note:** Requires table structure in PDF  
**Response:** Similar to DOCX

### 4. PDF → PPTX
```
POST /convert/pdf-to-pptx
```
**Response:**
```json
{
  "message": "PDF successfully converted to PPTX",
  "url": "http://…/storage/converted/… .pptx"
}
```

---

## 🧪 Testing with `curl`

**Compress:**
```bash
curl -F file=@my.pdf http://localhost:8000/compress
```
*(Similar usage for other endpoints.)*

---

## 🔐 Security & Production Tips

- Add validation (file size/type limits)
- Use authentication (API tokens)
- Secure `/storage/` for access control
- Add logging, monitoring, auto-clean-up of temp files
- Optionally wrap Python conversions in queues for scalability

---

## ⚖️ License

This project is **MIT licensed** — free for use and modification. Contributions are welcome!

---

## 🧠 Future Enhancements

- Add PDF → PNG/JPG image endpoint
- Support archiving outputs into `.zip`
- Integrate features for PDF manipulations (merge/split)
- Dockerize the stack for easy deployment
