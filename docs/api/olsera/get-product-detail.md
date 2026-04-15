# 📌 API Documentation: Get Product Detail

## 🔗 Endpoint

GET https://api-open.olsera.co.id/api/open-api/v1/en/product/detail

---

## 📖 Deskripsi

Endpoint ini digunakan untuk mengambil detail produk dari sistem Olsera.

---

## ⚙️ Request

### Method

GET

### Headers

Authorization: Bearer <access_token>  
Content-Type: application/json

---

## 📥 Request Parameters

| Parameter | Type | Required | Description                                                     |
| --------- | ---- | -------- | --------------------------------------------------------------- |
| id        | int  | Yes      | data ini didapatkan dari endpoint Product -> product list -> id |

---

## 🧪 Contoh Request (cURL)

```bash
curl -X GET "https://api-open.olsera.co.id/api/open-api/v1/en/product" \
  -H "Authorization: Bearer your_access_token_here" \
  -H "Content-Type: application/json"
```
