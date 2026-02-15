# RESTful API - Product Categories & Products

## Quick Start

### 1. Jalankan Migration
```bash
php artisan migrate
```

### 2. Jalankan Server
```bash
php artisan serve
```

### 3. Test API
Base URL: `http://localhost:8000/api`

---

## Endpoints

### Product Categories
- `GET /api/product-categories` - List semua categories
- `POST /api/product-categories` - Buat category baru
- `GET /api/product-categories/{id}` - Detail category
- `PATCH /api/product-categories/{id}` - Update category
- `DELETE /api/product-categories/{id}` - Hapus category

### Products
- `GET /api/products` - List semua products (dengan category)
- `POST /api/products` - Buat product baru
- `GET /api/products/{id}` - Detail product (dengan category)
- `PATCH /api/products/{id}` - Update product
- `DELETE /api/products/{id}` - Hapus product

---

## Contoh Request

### Create Category
```bash
POST /api/product-categories
Content-Type: application/json

{
    "name": "Electronics",
    "description": "Electronic devices and gadgets"
}
```

### Create Product
```bash
POST /api/products
Content-Type: application/json

{
    "name": "Laptop ASUS ROG",
    "price": 15000000,
    "category_id": 1
}
```

### Get Products (dengan category)
```bash
GET /api/products
```

Response:
```json
[
    {
        "id": 1,
        "name": "Laptop ASUS ROG",
        "price": "15000000.00",
        "category_id": 1,
        "category": {
            "id": 1,
            "name": "Electronics",
            "description": "Electronic devices and gadgets"
        }
    }
]
```

---

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── ProductCategoryController.php  ✅ CRUD lengkap
│   │   └── ProductController.php          ✅ CRUD + Eager Loading
│   └── Requests/
│       ├── StoreProductCategoryRequest.php   ✅ Validasi POST
│       └── UpdateProductCategoryRequest.php  ✅ Validasi PATCH
└── Models/
    ├── ProductCategory.php  ✅ hasMany(Product)
    └── Product.php          ✅ belongsTo(ProductCategory)

database/migrations/
├── 2026_02_15_165752_create_product_categories_table.php  ✅
└── 2026_02_15_165812_create_products_table.php            ✅ Foreign Key + CASCADE

routes/
└── api.php  ✅ apiResource routes
```

---

## Dokumentasi Lengkap

- **API_DOCUMENTATION.md** - Contoh request/response lengkap untuk semua endpoint
- **IMPLEMENTATION_GUIDE.md** - Penjelasan detail step-by-step dan alur request

---

## Fitur yang Sudah Diimplementasi

✅ Migration dengan foreign key constraint (CASCADE)  
✅ Model dengan relasi One-to-Many (hasMany & belongsTo)  
✅ Form Request untuk validasi  
✅ Controller dengan CRUD lengkap (index, show, store, update, destroy)  
✅ Eager loading untuk menghindari N+1 Problem  
✅ HTTP status code sesuai standar REST (200, 201, 404, 422)  
✅ Error handling untuk resource not found  
✅ API routes dengan apiResource()  
✅ Response JSON untuk semua endpoint  

---

## Testing

Lihat file **API_DOCUMENTATION.md** untuk contoh lengkap testing dengan:
- cURL
- Postman
- Thunder Client

Atau gunakan:
```bash
php artisan route:list --path=api
```
