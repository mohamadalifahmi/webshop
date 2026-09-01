<?php

use App\Http\Controllers\ProductController;
use App\Livewire\Account\Dashboard as AccountDashboard;
use App\Livewire\Account\MyOrders;
use App\Livewire\Account\OrderDetail;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\OrdersMonitor;
use App\Livewire\Admin\PayoutsManager as AdminPayouts;
use App\Livewire\Admin\ProductsModeration;
use App\Livewire\Admin\SellersManager;
use App\Livewire\Admin\SiteSettings;
use App\Livewire\Admin\CategoryManager;
use App\Livewire\Seller\Dashboard as SellerDashboard;
use App\Livewire\Seller\OrdersManager as SellerOrders;
use App\Livewire\Seller\PayoutsManager as SellerPayouts;
use App\Livewire\Seller\ProductsManager;
use App\Livewire\Seller\StoreSettings;
use App\Livewire\SellerApplication;
use App\Livewire\Storefront\CartPage;
use App\Livewire\Storefront\Catalog;
use App\Livewire\Storefront\Checkout;
use App\Livewire\Storefront\Home;
use App\Livewire\Storefront\ProductShow;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
Route::get('/shop', Catalog::class)->name('shop');
Route::get('/product/{product}', ProductShow::class)->name('product.show');
Route::get('/become-seller', SellerApplication::class)->middleware(['auth', 'throttle:10,1'])->name('become-seller');

// Currency preference toggle (USD / LBP) — persists to the session so the
// price component (x-price) actually reflects the chosen currency.
Route::get('/currency/{currency}', function (string $currency) {
    if (! in_array(strtoupper($currency), ['USD', 'LBP'], true)) {
        abort(404);
    }

    session(['currency' => strtoupper($currency)]);

    return redirect(url()->previous() ?: route('home'));
})->name('currency');

// Newsletter signup — stores safely, throttled, stateless (no account needed).
Route::post('/newsletter', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'email' => 'required|email:rfc,filter|max:190',
    ]);

    \App\Models\Setting::updateOrCreate(
        ['key' => 'newsletter_email:'.md5(strtolower($validated['email']))],
        ['value' => strtolower($validated['email'])],
    );

    return redirect(url()->previous() ?: route('home'))
        ->with('success', 'Welcome aboard — you are now subscribed to ASTRAGO news.');
})->middleware('throttle:5,1')->name('newsletter');

Route::get('/dashboard', fn () => redirect()->to(
    auth()->user()?->isAdmin() ? route('admin.dashboard')
        : (auth()->user()?->isSeller() ? route('seller.dashboard')
            : route('account.dashboard'))
))->middleware('auth')->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/account', AccountDashboard::class)->name('account.dashboard');
    Route::get('/cart', CartPage::class)->name('cart');
    Route::get('/checkout', Checkout::class)->middleware('throttle:6,1')->name('checkout');
    Route::post('/cart/items/{item}/quantity', [\App\Http\Controllers\CartController::class, 'updateQuantity'])
        ->name('cart.update-quantity');
    Route::post('/cart/items/{item}/remove', [\App\Http\Controllers\CartController::class, 'remove'])
        ->name('cart.remove');
    Route::get('/account/orders', MyOrders::class)->name('account.orders');
    Route::get('/account/orders/{number}', OrderDetail::class)->name('account.orders.show');
    Route::view('/account/profile', 'profile')->name('profile');

    // Seller hub — strict throttle protects the seller workflow too
    Route::get('/seller/apply', SellerApplication::class)->middleware('throttle:10,1')->name('seller.application.show');

    Route::prefix('seller')->name('seller.')->middleware(['role:seller', 'seller.approved', 'throttle:60,1'])->group(function () {
        Route::get('/', SellerDashboard::class)->name('dashboard');
        Route::get('/products', ProductsManager::class)->name('products');
        Route::patch('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::get('/orders', SellerOrders::class)->name('orders');
        Route::get('/payouts', SellerPayouts::class)->name('payouts');
        Route::get('/settings', StoreSettings::class)->name('settings');
    });

    // Admin panel — strict rate limit applies equally (hardening policy)
    Route::prefix('admin')->name('admin.')->middleware(['role:admin', 'throttle:60,1'])->group(function () {
        Route::get('/', AdminDashboard::class)->name('dashboard');
        Route::get('/sellers', SellersManager::class)->name('sellers');
        Route::get('/products', ProductsModeration::class)->name('products');
        Route::get('/orders', OrdersMonitor::class)->name('orders');
        Route::get('/payouts', AdminPayouts::class)->name('payouts');
        Route::get('/settings', SiteSettings::class)->name('settings');
        Route::get('/categories', CategoryManager::class)->name('categories');
    });
});

require __DIR__.'/auth.php';
