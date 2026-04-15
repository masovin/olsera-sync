# 📌 API Documentation: Token Endpoint

## 🔗 Endpoint

POST https://api-open.olsera.co.id/api/open-api/v1/id/token

---

## 📖 Deskripsi

Endpoint ini digunakan untuk mendapatkan **access token** yang diperlukan untuk autentikasi saat mengakses API Olsera lainnya.

---

## ⚙️ Request

### Method

POST

### Headers

Content-Type: application/json

---

## 📥 Request Body Parameters

| Parameter  | Type   | Required | Description                                                      |
| ---------- | ------ | -------- | ---------------------------------------------------------------- |
| app_id     | string | Yes      | ID aplikasi, didapatkan dari halaman `/console/app-list`         |
| secret_key | string | Yes      | Secret key aplikasi, didapatkan dari halaman `/console/app-list` |
| grant_type | string | Yes      | Harus diisi dengan nilai: `secret_key`                           |

---

## 🧪 Contoh Request

```json
{
  "app_id": "8R7yXPhDHJ0YG2S9FXkd",
  "secret_key": "Ag9naViTNoSEAw24BN7WLriZipxPzRzc",
  "grant_type": "secret_key"
}
```
