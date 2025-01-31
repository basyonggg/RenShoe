<?php

use App\Http\Controllers\ProductPageController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ProductListController;

use App\Http\Controllers\AddressController;

use App\Models\Product;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


//user route

Route::get('/', [UserController::class, 'index'])->name('home');

Route::get('/dashboard',[DashboardController::class,'index'])->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    
    //Updating the Profile Information
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update-account', [ProfileController::class, 'updateAccount'])->name('profile.updateAcc');
    Route::patch('/profile', [ProfileController::class, 'updateAddress'])->name('profile.updateAdd');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //checkout
    Route::prefix('checkout')->controller(CheckoutController::class)->group((function() {
        Route::post('order', 'store')->name('checkout.store');
        Route::get('success', 'success')->name('checkout.success');
        Route::get('cancel', 'cancel')->name('checkout.cancel');
    }));
});

//add to cart - localhost:8000/products
Route::prefix('cart')->controller(CartController::class)->group(function () {
    Route::get('view', 'view')->name('cart.view');
    Route::post('store/{product}', 'store')->name('cart.store');
    Route::patch('update/{product}', 'update')->name('cart.update');
    Route::delete('delete/{product}', 'delete')->name('cart.delete');

});

//routes for product list and filter - localhost:8000/products
Route::prefix('products')->controller(ProductListController::class)->group(function() {
    Route::get('/', 'index')->name('products.index');
});

//to show product page
Route::get('/detail/{id}', [ProductPageController::class, 'show'])->name('product.page');


//user route end

//admin route - localhost:8000/admin

Route::group(['prefix' => 'admin', 'middleware' => 'redirectAdmin'], function () {
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('admin.login.post');
    Route::get('logout', [AdminAuthController::class, 'logout'])->name('logout');
});

//for Stocks
Route::middleware(['auth', 'admin'])->get('/admin/api/total-stocks', [AdminController::class, 'getTotalStocks']);

//for the Registered Users
Route::middleware(['auth', 'admin'])->get('/admin/api/total-users', [AdminController::class, 'getTotalUsers']);

//for the Placed Orders
Route::middleware(['auth', 'admin'])->get('/admin/api/total-orders', [AdminController::class, 'getTotalOrders']);

//for the Total Earnings
Route::middleware(['auth', 'admin'])->get('/admin/api/total-sales', [AdminController::class, 'getTotalSales']);

//Displaying the Admin Dashboard
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');



    // product route
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::post('/products/store', [ProductController::class, 'store'])->name('admin.products.store');
    Route::put('/products/update/{id}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/image/{id}', [ProductController::class, 'deleteImage'])->name('admin.products.image.delete');
    Route::delete('/products/destroy/{id}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
});

//admin route end

require __DIR__.'/auth.php';
