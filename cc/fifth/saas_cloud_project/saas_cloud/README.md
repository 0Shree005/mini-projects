# NebulaCloud — SaaS Cloud Storage Mini Project
### Cloud Computing (410250) · SPPU BE Computer Engineering · Sem VI

---

## 📌 Objective

> Setup your own cloud for Software as a Service (SaaS) over the existing LAN.
> Implement a cloud controller using open-source technologies with HDFS-style storage.
> Divide files into segments/blocks and upload/download files on/from the cloud in **encrypted form**.

---

## 🗂️ Project Structure

```
saas_cloud/
├── app.py                  # Cloud Controller (Flask backend)
├── requirements.txt        # Python dependencies
├── templates/
│   └── index.html          # SaaS Dashboard (frontend)
└── cloud_storage/          # Auto-created — simulated HDFS datanode
    ├── metadata.json       # NameNode metadata
    ├── <file_id>/
    │   ├── chunk_0000.bin  # Encrypted chunk 0
    │   ├── chunk_0001.bin  # Encrypted chunk 1
    │   └── ...
    └── ...
```

---

## ▶️ How to Run

### 1. Install dependencies
```bash
pip install flask cryptography
```

### 2. Start the cloud controller
```bash
python app.py
```

### 3. Access the SaaS dashboard
- **On your machine:** http://localhost:5000
- **From any other PC on LAN:** http://\<your-IP\>:5000

To find your IP on Windows: `ipconfig` → IPv4 Address  
To find your IP on Linux/Mac: `hostname -I`

---

## 🏗️ Architecture

```
   ┌─────────────┐      HTTP/LAN       ┌───────────────────────┐
   │   Client    │ ─────────────────►  │   Cloud Controller    │
   │  (Browser)  │ ◄─────────────────  │   Flask (Python)      │
   └─────────────┘                     └────────┬──────────────┘
                                                │
                              ┌─────────────────▼──────────────────┐
                              │           Cloud Controller          │
                              │                                     │
                              │  1. Split file into 1MB chunks      │
                              │  2. Encrypt each chunk (AES-CBC)    │
                              │  3. Save chunks to DataNode         │
                              │  4. Update NameNode (metadata.json) │
                              └───────────┬────────────────────────┘
                                          │
                   ┌──────────────────────┼──────────────────────┐
                   ▼                      ▼                      ▼
          ┌──────────────┐     ┌──────────────────┐    ┌──────────────┐
          │   DataNode   │     │    DataNode       │    │   NameNode   │
          │  (chunk dir) │     │   (chunk dir)     │    │(metadata.json│
          │chunk_0000.bin│     │  chunk_0001.bin   │    │ file index,  │
          │chunk_0001.bin│     │  chunk_0002.bin   │    │ chunk lists) │
          └──────────────┘     └──────────────────┘    └──────────────┘
```

---

## ⚙️ Core Concepts Implemented

### 1. HDFS-style File Chunking
Just like HDFS (Hadoop Distributed File System) splits files into blocks (default 128MB), this project splits uploaded files into **1MB chunks**:

```python
CHUNK_SIZE = 1 * 1024 * 1024   # 1 MB

def split_and_store(file_bytes, filename):
    num_chunks = math.ceil(len(file_bytes) / CHUNK_SIZE)
    for i in range(num_chunks):
        chunk = file_bytes[i * CHUNK_SIZE : (i+1) * CHUNK_SIZE]
        encrypted = encrypt_bytes(chunk)
        save(encrypted, f"chunk_{i:04d}.bin")
```

### 2. AES-CBC Encryption
Every chunk is encrypted using **AES (Advanced Encryption Standard)** in **CBC (Cipher Block Chaining)** mode before being written to disk:

```python
from cryptography.hazmat.primitives.ciphers import Cipher, algorithms, modes

def encrypt_bytes(data):
    padder = PKCS7(128).padder()
    padded = padder.update(data) + padder.finalize()
    cipher = Cipher(AES(SECRET_KEY), CBC(IV))
    enc = cipher.encryptor()
    return enc.update(padded) + enc.finalize()
```

- **Key:** 16-byte AES-128 key
- **IV:** 16-byte initialization vector
- **Padding:** PKCS7 to align to 16-byte AES block size

### 3. NameNode — Metadata Management
A `metadata.json` file acts like the HDFS NameNode — it tracks which chunks belong to which file:

```json
{
  "abc123def456": {
    "file_id": "abc123def456",
    "filename": "report.pdf",
    "size": 5242880,
    "num_chunks": 5,
    "chunks": ["chunk_0000.bin", "chunk_0001.bin", ...],
    "checksum": "md5hash...",
    "uploaded_at": "2024-11-20T14:32:00"
  }
}
```

### 4. Download & Reassembly
On download, chunks are reassembled in order, each decrypted, then joined back:

```python
def reassemble(file_id):
    result = b""
    for chunk_file in entry["chunks"]:
        encrypted = read(chunk_file)
        result += decrypt_bytes(encrypted)
    return result
```

### 5. SaaS Model
The web UI runs on Flask and is accessible to **any machine on the same LAN**, making it a true Software-as-a-Service deployment — no software installation needed on client machines.

---

## 🔌 API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/` | SaaS Dashboard (HTML UI) |
| POST | `/api/upload` | Upload + chunk + encrypt a file |
| GET | `/api/files` | List all stored files |
| GET | `/api/download/<id>` | Decrypt + reassemble + download |
| DELETE | `/api/delete/<id>` | Delete file and all its chunks |
| GET | `/api/info/<id>` | Get file metadata & chunk list |
| GET | `/api/stats` | Cloud statistics (total files, disk used, etc.) |

---

## 🎓 Viva Questions & Answers

**Q: What is SaaS?**  
A: Software as a Service — software is hosted on a server and accessed by users over a network (like this web app), without any local installation.

**Q: What is HDFS and how does your project simulate it?**  
A: Hadoop Distributed File System splits files into blocks stored across multiple DataNodes, with a NameNode tracking metadata. This project splits files into 1MB chunks (DataNodes = directories), tracked in metadata.json (NameNode).

**Q: Why is encryption done per-chunk and not on the whole file?**  
A: Chunked encryption allows parallel processing, and in a distributed system, each node can independently decrypt its chunk. It also limits exposure — if one chunk is compromised, others remain secure.

**Q: What encryption algorithm is used?**  
A: AES-128 in CBC (Cipher Block Chaining) mode with PKCS7 padding. AES is a symmetric encryption standard. CBC chains each block's encryption to the previous, adding data integrity.

**Q: How does the cloud work over LAN?**  
A: Flask binds to `0.0.0.0:5000`, making the server accessible from any IP on the same network. Client browsers on other machines connect via HTTP to the server's LAN IP.

**Q: What is the role of metadata.json in your system?**  
A: It acts as the NameNode — storing the mapping between files and their chunks, along with checksums, timestamps, and sizes. Without it, reassembly is impossible.

**Q: How do you verify file integrity?**  
A: An MD5 checksum of the original file is computed on upload and stored. On download, the reassembled data can be compared against this checksum to detect corruption.

---

## 🔧 Technologies Used

| Component | Technology |
|-----------|-----------|
| Language | Python 3 |
| Cloud Controller (Backend) | Flask |
| Encryption | `cryptography` library (AES-CBC) |
| Metadata Store | JSON (NameNode simulation) |
| Frontend (SaaS UI) | HTML5, CSS3, Vanilla JS |
| File Storage | Local filesystem (DataNode simulation) |
| Network | HTTP over LAN |

---

*SPPU BE Computer Engineering · Cloud Computing (410250) · Sem VI*
