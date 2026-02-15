# RESTful API Documentation - Product Categories & Products

## Penjelasan Alur Request → Response

### Alur Umum:
1. **Client** mengirim HTTP Request ke endpoint API
2. **Router** (api.php) menerima request dan mengarahkan ke Controller yang sesuai
3. **Controller** menerima request dan melakukan:
   - Validasi data (menggunakan Form Request)
   - Operasi ke Model (CRUD)
4. **Model** berinteraksi dengan Database melalui Eloquent ORM
5. **Database** mengembalikan data ke Model
6. **Model** mengembalikan data ke Controller
7. **Controller** memformat response (JSON) dengan HTTP status code yang sesuai
8. **Client** menerima response JSON

---

## Database Schema

### Tabel: product_categories
```sql
- id (bigint, primary key, auto increment)
- name (varchar 255, required)
- description (text, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

### Tabel: products
```sql
- id (bigint, primary key, auto increment)
- name (varchar 255, required)
- price (decimal 15,2, required)
- category_id (bigint, foreign key → product_categories.id)
- created_at (timestamp)
- updated_at (timestamp)

Foreign Key Constraints:
- ON UPDATE CASCADE
- ON DELETE CASCADE
```

---

## Relasi Model

### ProductCategory Model
```php
// Relasi One-to-Many
public function products()
{
    return $this->hasMany(Product::class, 'category_id');
}
```

### Product Model
```php
// Relasi Many-to-One (Belongs To)
public function category()
{
    return $this->belongsTo(ProductCategory::class, 'category_id');
}
```

---

## API Endpoints - Product Categories

### 1. GET /api/product-categories
**Deskripsi:** Mengambil seluruh data category

**Request:**
```http
GET http://localhost:8000/api/product-categories
Accept: application/json
```

**Response (200 OK):**
```json
[
    {
        "id": 1,
        "name": "Electronics",
        "description": "Electronic devices and gadgets",
        "created_at": "2026-02-15T16:58:00.000000Z",
        "updated_at": "2026-02-15T16:58:00.000000Z"
    },
    {
        "id": 2,
        "name": "Furniture",
        "description": "Home and office furniture",
        "created_at": "2026-02-15T16:59:00.000000Z",
        "updated_at": "2026-02-15T16:59:00.000000Z"
    }
]
```

---

### 2. GET /api/product-categories/{id}
**Deskripsi:** Mengambil category berdasarkan ID

**Request:**
```http
GET http://localhost:8000/api/product-categories/1
Accept: application/json
```

**Response (200 OK):**
```json
{
    "id": 1,
    "name": "Electronics",
    "description": "Electronic devices and gadgets",
    "created_at": "2026-02-15T16:58:00.000000Z",
    "updated_at": "2026-02-15T16:58:00.000000Z"
}
```

**Response (404 Not Found):**
```json
{
    "message": "Product category not found"
}
```

---

### 3. POST /api/product-categories
**Deskripsi:** Menambahkan category baru

**Request:**
```http
POST http://localhost:8000/api/product-categories
Content-Type: application/json
Accept: application/json

{
    "name": "Electronics",
    "description": "Electronic devices and gadgets"
}
```

**Validasi Rules:**
- `name`: required, string, max 255 characters
- `description`: nullable, string

**Response (201 Created):**
```json
{
    "id": 1,
    "name": "Electronics",
    "description": "Electronic devices and gadgets",
    "created_at": "2026-02-15T16:58:00.000000Z",
    "updated_at": "2026-02-15T16:58:00.000000Z"
}
```

**Response (422 Unprocessable Entity) - Validation Error:**
```json
{
    "message": "The name field is required.",
    "errors": {
        "name": [
            "The name field is required."
        ]
    }
}
```

---

### 4. PATCH /api/product-categories/{id}
**Deskripsi:** Update sebagian data category (partial update)

**Request:**
```http
PATCH http://localhost:8000/api/product-categories/1
Content-Type: application/json
Accept: application/json

{
    "description": "Updated description for electronics"
}
```

**Validasi Rules:**
- `name`: sometimes|required, string, max 255 characters
- `description`: nullable, string

**Response (200 OK):**
```json
{
    "id": 1,
    "name": "Electronics",
    "description": "Updated description for electronics",
    "created_at": "2026-02-15T16:58:00.000000Z",
    "updated_at": "2026-02-15T17:05:00.000000Z"
}
```

**Response (404 Not Found):**
```json
{
    "message": "Product category not found"
}
```

---

### 5. DELETE /api/product-categories/{id}
**Deskripsi:** Menghapus category

**Request:**
```http
DELETE http://localhost:8000/api/product-categories/1
Accept: application/json
```

**Response (200 OK):**
```json
{
    "message": "Product category deleted successfully"
}
```

**Response (404 Not Found):**
```json
{
    "message": "Product category not found"
}
```

**Note:** Karena ada constraint CASCADE, semua products yang terkait dengan category ini akan ikut terhapus.

---

## API Endpoints - Products

### 1. GET /api/products
**Deskripsi:** Mengambil seluruh data products dengan category (eager loading)

**Request:**
```http
GET http://localhost:8000/api/products
Accept: application/json
```

**Response (200 OK):**
```json
[
    {
        "id": 1,
        "name": "Laptop ASUS ROG",
        "price": "15000000.00",
        "category_id": 1,
        "created_at": "2026-02-15T17:00:00.000000Z",
        "updated_at": "2026-02-15T17:00:00.000000Z",
        "category": {
            "id": 1,
            "name": "Electronics",
            "description": "Electronic devices and gadgets",
            "created_at": "2026-02-15T16:58:00.000000Z",
            "updated_at": "2026-02-15T16:58:00.000000Z"
        }
    },
    {
        "id": 2,
        "name": "Office Chair",
        "price": "2500000.00",
        "category_id": 2,
        "created_at": "2026-02-15T17:01:00.000000Z",
        "updated_at": "2026-02-15T17:01:00.000000Z",
        "category": {
            "id": 2,
            "name": "Furniture",
            "description": "Home and office furniture",
            "created_at": "2026-02-15T16:59:00.000000Z",
            "updated_at": "2026-02-15T16:59:00.000000Z"
        }
    }
]
```

---

### 2. GET /api/products/{id}
**Deskripsi:** Mengambil product berdasarkan ID dengan category (eager loading)

**Request:**
```http
GET http://localhost:8000/api/products/1
Accept: application/json
```

**Response (200 OK):**
```json
{
    "id": 1,
    "name": "Laptop ASUS ROG",
    "price": "15000000.00",
    "category_id": 1,
    "created_at": "2026-02-15T17:00:00.000000Z",
    "updated_at": "2026-02-15T17:00:00.000000Z",
    "category": {
        "id": 1,
        "name": "Electronics",
        "description": "Electronic devices and gadgets",
        "created_at": "2026-02-15T16:58:00.000000Z",
        "updated_at": "2026-02-15T16:58:00.000000Z"
    }
}
```

**Response (404 Not Found):**
```json
{
    "message": "Product not found"
}
```

---

### 3. POST /api/products
**Deskripsi:** Menambahkan product baru

**Request:**
```http
POST http://localhost:8000/api/products
Content-Type: application/json
Accept: application/json

{
    "name": "Laptop ASUS ROG",
    "price": 15000000,
    "category_id": 1
}
```

**Validasi Rules:**
- `name`: required, string, max 255 characters
- `price`: required, numeric, min 0
- `category_id`: required, exists in product_categories table

**Response (201 Created):**
```json
{
    "id": 1,
    "name": "Laptop ASUS ROG",
    "price": "15000000.00",
    "category_id": 1,
    "created_at": "2026-02-15T17:00:00.000000Z",
    "updated_at": "2026-02-15T17:00:00.000000Z",
    "category": {
        "id": 1,
        "name": "Electronics",
        "description": "Electronic devices and gadgets",
        "created_at": "2026-02-15T16:58:00.000000Z",
        "updated_at": "2026-02-15T16:58:00.000000Z"
    }
}
```

**Response (422 Unprocessable Entity) - Validation Error:**
```json
{
    "message": "The category id field must exist in product categories.",
    "errors": {
        "category_id": [
            "The selected category id is invalid."
        ]
    }
}
```

---

### 4. PATCH /api/products/{id}
**Deskripsi:** Update sebagian data product

**Request:**
```http
PATCH http://localhost:8000/api/products/1
Content-Type: application/json
Accept: application/json

{
    "price": 14500000
}
```

**Response (200 OK):**
```json
{
    "id": 1,
    "name": "Laptop ASUS ROG",
    "price": "14500000.00",
    "category_id": 1,
    "created_at": "2026-02-15T17:00:00.000000Z",
    "updated_at": "2026-02-15T17:10:00.000000Z",
    "category": {
        "id": 1,
        "name": "Electronics",
        "description": "Electronic devices and gadgets",
        "created_at": "2026-02-15T16:58:00.000000Z",
        "updated_at": "2026-02-15T16:58:00.000000Z"
    }
}
```

---

### 5. DELETE /api/products/{id}
**Deskripsi:** Menghapus product

**Request:**
```http
DELETE http://localhost:8000/api/products/1
Accept: application/json
```

**Response (200 OK):**
```json
{
    "message": "Product deleted successfully"
}
```

---

## HTTP Status Codes

| Status Code | Penggunaan |
|-------------|------------|
| 200 OK | Request berhasil (GET, PATCH, DELETE) |
| 201 Created | Resource berhasil dibuat (POST) |
| 404 Not Found | Resource tidak ditemukan |
| 422 Unprocessable Entity | Validation error |

---

## Eager Loading Explanation

### Tanpa Eager Loading (N+1 Problem):
```php
$products = Product::all(); // 1 query
foreach ($products as $product) {
    echo $product->category->name; // N queries (1 per product)
}
// Total: 1 + N queries
```

### Dengan Eager Loading:
```php
$products = Product::with('category')->get(); // 2 queries total
foreach ($products as $product) {
    echo $product->category->name; // No additional query
}
// Total: 2 queries (1 untuk products, 1 untuk categories)
```

---

## Testing dengan cURL

### Create Category:
```bash
curl -X POST http://localhost:8000/api/product-categories \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name":"Electronics","description":"Electronic devices"}'
```

### Create Product:
```bash
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name":"Laptop","price":15000000,"category_id":1}'
```

### Get All Products with Categories:
```bash
curl -X GET http://localhost:8000/api/products \
  -H "Accept: application/json"
```

---

## File Structure

```
modul-1/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── ProductCategoryController.php
│   │   │   └── ProductController.php
│   │   └── Requests/
│   │       ├── StoreProductCategoryRequest.php
│   │       └── UpdateProductCategoryRequest.php
│   └── Models/
│       ├── Product.php
│       └── ProductCategory.php
├── database/
│   └── migrations/
│       ├── 2026_02_15_165752_create_product_categories_table.php
│       └── 2026_02_15_165812_create_products_table.php
└── routes/
    └── api.php
```
