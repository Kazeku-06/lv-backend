# Implementation Guide - RESTful API Product Categories

## Penjelasan Step-by-Step

### Step 1: Database Migration

#### Migration: create_product_categories_table.php
```php
Schema::create('product_categories', function (Blueprint $table) {
    $table->id();                           // Primary key auto increment
    $table->string('name');                 // Nama category (required)
    $table->text('description')->nullable(); // Deskripsi (optional)
    $table->timestamps();                   // created_at & updated_at
});
```

**Penjelasan:**
- `id()`: Membuat kolom id sebagai primary key dengan auto increment
- `string('name')`: Kolom varchar(255) untuk nama category
- `text('description')->nullable()`: Kolom text yang boleh kosong
- `timestamps()`: Otomatis membuat kolom created_at dan updated_at

#### Migration: create_products_table.php
```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->decimal('price', 15, 2);        // Harga dengan 2 desimal
    $table->foreignId('category_id')        // Foreign key ke product_categories
          ->constrained('product_categories') // Referensi ke tabel product_categories
          ->onUpdate('cascade')              // Update otomatis jika parent berubah
          ->onDelete('cascade');             // Hapus otomatis jika parent dihapus
    $table->timestamps();
});
```

**Penjelasan Foreign Key:**
- `foreignId('category_id')`: Membuat kolom category_id sebagai foreign key
- `constrained('product_categories')`: Menghubungkan ke tabel product_categories
- `onUpdate('cascade')`: Jika id di product_categories berubah, category_id di products ikut berubah
- `onDelete('cascade')`: Jika category dihapus, semua products dengan category tersebut ikut terhapus

**Menjalankan Migration:**
```bash
php artisan migrate
```

---

### Step 2: Model dengan Relasi

#### Model: ProductCategory.php
```php
class ProductCategory extends Model
{
    protected $fillable = ['name', 'description'];

    // Relasi One-to-Many
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
```

**Penjelasan:**
- `$fillable`: Kolom yang boleh diisi secara mass assignment
- `hasMany()`: Satu category memiliki banyak products
- Parameter kedua `'category_id'`: Nama foreign key di tabel products

**Cara Menggunakan:**
```php
$category = ProductCategory::find(1);
$products = $category->products; // Ambil semua products dari category ini
```

#### Model: Product.php
```php
class Product extends Model
{
    protected $fillable = ['name', 'price', 'category_id'];

    // Relasi Many-to-One (Belongs To)
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }
}
```

**Penjelasan:**
- `belongsTo()`: Product dimiliki oleh satu category
- Parameter kedua `'category_id'`: Nama foreign key di tabel products

**Cara Menggunakan:**
```php
$product = Product::find(1);
$category = $product->category; // Ambil category dari product ini
```

---

### Step 3: Form Request untuk Validasi

#### StoreProductCategoryRequest.php
```php
public function authorize(): bool
{
    return true; // Izinkan semua user (ubah sesuai kebutuhan)
}

public function rules(): array
{
    return [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
    ];
}
```

**Penjelasan Validasi:**
- `required`: Field wajib diisi
- `string`: Harus berupa string
- `max:255`: Maksimal 255 karakter
- `nullable`: Boleh kosong/null

#### UpdateProductCategoryRequest.php
```php
public function rules(): array
{
    return [
        'name' => 'sometimes|required|string|max:255',
        'description' => 'nullable|string',
    ];
}
```

**Penjelasan:**
- `sometimes`: Validasi hanya dijalankan jika field ada di request
- Cocok untuk PATCH (partial update)

---

### Step 4: Controller dengan CRUD Operations

#### ProductCategoryController.php

##### 1. index() - GET /api/product-categories
```php
public function index(): JsonResponse
{
    $categories = ProductCategory::all();
    return response()->json($categories, 200);
}
```

**Alur:**
1. Ambil semua data dari tabel product_categories
2. Return sebagai JSON dengan status 200

##### 2. store() - POST /api/product-categories
```php
public function store(StoreProductCategoryRequest $request): JsonResponse
{
    $category = ProductCategory::create($request->validated());
    return response()->json($category, 201);
}
```

**Alur:**
1. Request masuk ke StoreProductCategoryRequest untuk validasi
2. Jika validasi gagal, otomatis return 422 dengan error messages
3. Jika validasi sukses, `$request->validated()` berisi data yang sudah tervalidasi
4. `ProductCategory::create()` insert data ke database
5. Return data yang baru dibuat dengan status 201 (Created)

##### 3. show() - GET /api/product-categories/{id}
```php
public function show(string $id): JsonResponse
{
    $category = ProductCategory::find($id);
    
    if (!$category) {
        return response()->json([
            'message' => 'Product category not found'
        ], 404);
    }
    
    return response()->json($category, 200);
}
```

**Alur:**
1. Cari category berdasarkan id
2. Jika tidak ditemukan, return 404
3. Jika ditemukan, return data dengan status 200

##### 4. update() - PATCH /api/product-categories/{id}
```php
public function update(UpdateProductCategoryRequest $request, string $id): JsonResponse
{
    $category = ProductCategory::find($id);
    
    if (!$category) {
        return response()->json([
            'message' => 'Product category not found'
        ], 404);
    }
    
    $category->update($request->validated());
    return response()->json($category, 200);
}
```

**Alur:**
1. Cari category berdasarkan id
2. Jika tidak ditemukan, return 404
3. Validasi data menggunakan UpdateProductCategoryRequest
4. Update data dengan `$category->update()`
5. Return data yang sudah diupdate dengan status 200

##### 5. destroy() - DELETE /api/product-categories/{id}
```php
public function destroy(string $id): JsonResponse
{
    $category = ProductCategory::find($id);
    
    if (!$category) {
        return response()->json([
            'message' => 'Product category not found'
        ], 404);
    }
    
    $category->delete();
    
    return response()->json([
        'message' => 'Product category deleted successfully'
    ], 200);
}
```

**Alur:**
1. Cari category berdasarkan id
2. Jika tidak ditemukan, return 404
3. Hapus data dengan `$category->delete()`
4. Karena ada CASCADE, semua products terkait ikut terhapus
5. Return success message dengan status 200

---

### Step 5: ProductController dengan Eager Loading

#### Eager Loading di index() dan show()
```php
public function index(): JsonResponse
{
    $products = Product::with('category')->get();
    return response()->json($products, 200);
}

public function show(string $id): JsonResponse
{
    $product = Product::with('category')->find($id);
    
    if (!$product) {
        return response()->json([
            'message' => 'Product not found'
        ], 404);
    }
    
    return response()->json($product, 200);
}
```

**Penjelasan Eager Loading:**

Tanpa `with('category')`:
```php
$products = Product::all(); // 1 query
// Saat akses $product->category, akan ada query tambahan untuk setiap product
// Total: 1 + N queries (N+1 Problem)
```

Dengan `with('category')`:
```php
$products = Product::with('category')->get(); // 2 queries
// Query 1: SELECT * FROM products
// Query 2: SELECT * FROM product_categories WHERE id IN (1,2,3,...)
// Total: 2 queries saja, lebih efisien!
```

#### Validasi di ProductController
```php
public function store(Request $request): JsonResponse
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'category_id' => 'required|exists:product_categories,id',
    ]);
    
    $product = Product::create($validated);
    $product->load('category'); // Load relasi setelah create
    
    return response()->json($product, 201);
}
```

**Penjelasan:**
- `exists:product_categories,id`: Validasi bahwa category_id harus ada di tabel product_categories
- `$product->load('category')`: Load relasi category setelah product dibuat
- Ini memastikan response menyertakan data category

---

### Step 6: Routes API

#### routes/api.php
```php
Route::apiResource('product-categories', ProductCategoryController::class);
Route::apiResource('products', ProductController::class);
```

**Penjelasan `apiResource()`:**

Satu baris `apiResource()` otomatis membuat 5 routes:

| Method | URI | Action | Route Name |
|--------|-----|--------|------------|
| GET | /api/product-categories | index | product-categories.index |
| POST | /api/product-categories | store | product-categories.store |
| GET | /api/product-categories/{id} | show | product-categories.show |
| PUT/PATCH | /api/product-categories/{id} | update | product-categories.update |
| DELETE | /api/product-categories/{id} | destroy | product-categories.destroy |

**Perbedaan dengan `resource()`:**
- `apiResource()`: Tidak membuat route create dan edit (untuk form)
- `resource()`: Membuat 7 routes termasuk create dan edit

---

### Step 7: Konfigurasi API Routes

#### bootstrap/app.php
```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',  // Tambahkan ini
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
```

**Penjelasan:**
- Menambahkan `api: __DIR__.'/../routes/api.php'`
- Semua routes di api.php otomatis mendapat prefix `/api`
- Middleware `api` otomatis diterapkan (throttle, json response, dll)

---

## Alur Lengkap Request → Response

### Contoh: POST /api/product-categories

```
1. Client mengirim request:
   POST http://localhost:8000/api/product-categories
   Body: {"name": "Electronics", "description": "Gadgets"}

2. Laravel Router (api.php):
   - Menerima request
   - Mencocokkan dengan route: POST /api/product-categories
   - Mengarahkan ke ProductCategoryController@store

3. Controller (ProductCategoryController):
   - Method store() dipanggil
   - Parameter StoreProductCategoryRequest di-inject

4. Form Request (StoreProductCategoryRequest):
   - authorize() dipanggil → return true (lanjut)
   - rules() dipanggil → validasi data
   - Jika gagal: return 422 dengan error messages
   - Jika sukses: lanjut ke controller

5. Controller (lanjutan):
   - $request->validated() berisi data yang sudah tervalidasi
   - ProductCategory::create() dipanggil

6. Model (ProductCategory):
   - Eloquent ORM membuat query INSERT
   - Data disimpan ke database

7. Database:
   - Eksekusi query INSERT
   - Return data yang baru dibuat (dengan id, timestamps)

8. Model → Controller:
   - Data dari database dikembalikan ke controller

9. Controller → Client:
   - response()->json($category, 201)
   - Return JSON dengan status 201 Created

10. Client menerima response:
    {
        "id": 1,
        "name": "Electronics",
        "description": "Gadgets",
        "created_at": "2026-02-15T17:00:00.000000Z",
        "updated_at": "2026-02-15T17:00:00.000000Z"
    }
```

---

## HTTP Status Codes yang Digunakan

| Status | Kode | Penggunaan |
|--------|------|------------|
| OK | 200 | Request berhasil (GET, PATCH, DELETE) |
| Created | 201 | Resource berhasil dibuat (POST) |
| Not Found | 404 | Resource tidak ditemukan |
| Unprocessable Entity | 422 | Validation error (otomatis dari Laravel) |

---

## Testing API

### Menggunakan Postman atau Thunder Client:

1. **Create Category:**
   - Method: POST
   - URL: http://localhost:8000/api/product-categories
   - Headers: Content-Type: application/json
   - Body (raw JSON):
     ```json
     {
         "name": "Electronics",
         "description": "Electronic devices and gadgets"
     }
     ```

2. **Create Product:**
   - Method: POST
   - URL: http://localhost:8000/api/products
   - Body:
     ```json
     {
         "name": "Laptop ASUS ROG",
         "price": 15000000,
         "category_id": 1
     }
     ```

3. **Get All Products (with categories):**
   - Method: GET
   - URL: http://localhost:8000/api/products
   - Response akan menyertakan data category untuk setiap product

4. **Update Category (Partial):**
   - Method: PATCH
   - URL: http://localhost:8000/api/product-categories/1
   - Body:
     ```json
     {
         "description": "Updated description"
     }
     ```

5. **Delete Category:**
   - Method: DELETE
   - URL: http://localhost:8000/api/product-categories/1
   - Semua products dengan category_id=1 akan ikut terhapus (CASCADE)

---

## Troubleshooting

### Error: "Product category not found"
- Pastikan id yang digunakan ada di database
- Cek dengan: `php artisan tinker` → `ProductCategory::all()`

### Error: "The selected category id is invalid"
- category_id yang dikirim tidak ada di tabel product_categories
- Buat category terlebih dahulu sebelum membuat product

### Error: Route not found
- Pastikan sudah menambahkan `api: __DIR__.'/../routes/api.php'` di bootstrap/app.php
- Jalankan: `php artisan route:clear`

### Error: SQLSTATE[23000]: Integrity constraint violation
- Terjadi saat mencoba insert product dengan category_id yang tidak ada
- Pastikan category_id valid

---

## Best Practices

1. **Selalu gunakan Form Request untuk validasi**
   - Memisahkan logic validasi dari controller
   - Lebih mudah di-maintain

2. **Gunakan Eager Loading untuk relasi**
   - Hindari N+1 Problem
   - Lebih efisien untuk performa

3. **Gunakan HTTP Status Code yang sesuai**
   - 200: Success (GET, PATCH, DELETE)
   - 201: Created (POST)
   - 404: Not Found
   - 422: Validation Error

4. **Selalu cek apakah resource ada sebelum update/delete**
   - Return 404 jika tidak ditemukan
   - Berikan error message yang jelas

5. **Gunakan apiResource() untuk RESTful API**
   - Otomatis membuat 5 routes standar
   - Konsisten dengan konvensi REST

6. **Gunakan Foreign Key Constraints**
   - Menjaga integritas data
   - CASCADE untuk auto delete/update

---

## Kesimpulan

Implementasi ini sudah mencakup:
✅ Migration dengan foreign key dan cascade
✅ Model dengan relasi One-to-Many
✅ Form Request untuk validasi
✅ Controller dengan CRUD lengkap
✅ Eager loading untuk performa
✅ HTTP status code yang sesuai standar REST
✅ Error handling untuk 404
✅ API routes dengan apiResource()

Semua endpoint sudah siap digunakan dan mengikuti best practices Laravel!
