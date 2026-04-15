# 📌 API Documentation: Refresh Token Endpoint

## 🔗 Endpoint

POST https://api-open.olsera.co.id/api/open-api/v1/id/token

---

## 📖 Deskripsi

Endpoint ini digunakan untuk melakukan **refresh access token** menggunakan `refresh_token` yang telah didapatkan sebelumnya.

---

## ⚙️ Request

### Method

POST

### Headers

Content-Type: application/json

---

## 📥 Request Body Parameters

| Parameter     | Type   | Required | Description                                                    |
| ------------- | ------ | -------- | -------------------------------------------------------------- |
| refresh_token | string | Yes      | Token refresh yang didapatkan saat pertama kali generate token |
| grant_type    | string | Yes      | Harus diisi dengan nilai: `refresh_token`                      |

---

## 🧪 Contoh Request

```json
{
  "refresh_token": "{{refresh_token_openapi}}",
  "grant_type": "refresh_token"
}
```
