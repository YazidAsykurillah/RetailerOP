<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth']], function() {
    Route::resource('users', App\Http\Controllers\UserController::class);
    Route::resource('roles', App\Http\Controllers\RoleController::class);
    Route::resource('permissions', App\Http\Controllers\PermissionController::class);
});

// Admin Store Management Routes
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function() {
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('brands', App\Http\Controllers\Admin\BrandController::class);
    Route::resource('suppliers', App\Http\Controllers\Admin\SupplierController::class);
    
    // Purchase Management Routes
    Route::get('purchases/search-products', [App\Http\Controllers\Admin\PurchaseController::class, 'searchProducts'])->name('purchases.search-products');
    Route::post('purchases/{purchase}/receive', [App\Http\Controllers\Admin\PurchaseController::class, 'receiveItems'])->name('purchases.receive');
    Route::resource('purchases', App\Http\Controllers\Admin\PurchaseController::class);
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
    Route::resource('products.variants', App\Http\Controllers\Admin\ProductVariantController::class)->except(['show']);
    Route::get('products/{product}/variants/{variant}/print-barcode', [App\Http\Controllers\Admin\ProductVariantController::class, 'printBarcode'])->name('products.variants.print-barcode');
    Route::resource('variant-types', App\Http\Controllers\Admin\VariantTypeController::class)->except(['show']);
    Route::resource('variant-types.values', App\Http\Controllers\Admin\VariantValueController::class)->except(['show']);
    Route::resource('customer-groups', App\Http\Controllers\Admin\CustomerGroupController::class);
    Route::resource('customers', App\Http\Controllers\Admin\CustomerController::class);

    // Stock Management Routes
    Route::prefix('stock')->name('stock.')->group(function() {
        Route::get('/', [App\Http\Controllers\Admin\StockController::class, 'index'])->name('index');
        Route::get('/in', [App\Http\Controllers\Admin\StockController::class, 'createIn'])->name('in');
        Route::post('/in', [App\Http\Controllers\Admin\StockController::class, 'storeIn'])->name('in.store');
        Route::get('/out', [App\Http\Controllers\Admin\StockController::class, 'createOut'])->name('out');
        Route::post('/out', [App\Http\Controllers\Admin\StockController::class, 'storeOut'])->name('out.store');
        Route::get('/search-products', [App\Http\Controllers\Admin\StockController::class, 'searchProducts'])->name('search-products');
        Route::get('/{variant}/history', [App\Http\Controllers\Admin\StockController::class, 'history'])->name('history');
    });

    // POS Routes
    Route::prefix('pos')->name('pos.')->group(function() {
        Route::get('/', [App\Http\Controllers\Admin\POSController::class, 'index'])->name('index');
        Route::get('/search-products', [App\Http\Controllers\Admin\POSController::class, 'searchProducts'])->name('search-products');
        Route::get('/find-by-sku', [App\Http\Controllers\Admin\POSController::class, 'findBySku'])->name('find-by-sku');
        Route::get('/product/{id}', [App\Http\Controllers\Admin\POSController::class, 'getProductDetails'])->name('product-details');
        Route::get('/by-category', [App\Http\Controllers\Admin\POSController::class, 'getByCategory'])->name('by-category');
        Route::post('/checkout', [App\Http\Controllers\Admin\POSController::class, 'processTransaction'])->name('checkout');
    });

    // Account Settings Routes
    Route::get('/settings', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/settings', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/settings/password', [App\Http\Controllers\Admin\ProfileController::class, 'editPassword'])->name('profile.password.edit');
    Route::put('/settings/password', [App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Transaction History Routes
    Route::resource('transactions', App\Http\Controllers\Admin\TransactionController::class)->only(['index', 'show']);
    Route::get('/transactions/{transaction}/print', [App\Http\Controllers\Admin\TransactionController::class, 'printReceipt'])->name('transactions.print');
});

