<?php

use App\Http\Controllers\Admin\CityPageController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CategoryAttributeController;
use App\Http\Controllers\Admin\FeedbackController as AdminFeedbackController;
use App\Http\Controllers\Admin\ListingController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Auth\PortalAuthController;
use App\Http\Controllers\Customer\FeedbackController as CustomerFeedbackController;
use App\Http\Controllers\Customer\CustomerDashboardController;
use App\Http\Controllers\Owner\FeedbackController as OwnerFeedbackController;
use App\Http\Controllers\Owner\OwnerDashboardController;
use App\Http\Controllers\Portal\MessageCenterController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\Support\SupportDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/ilanlar', [PublicController::class, 'listings'])->name('listing.index');
Route::get('/hizmet/{slug}/{city}/{district}', [PublicController::class, 'serviceCategoryCityDistrict'])->name('service-category.city-district');
Route::get('/hizmet/{slug}/{city}', [PublicController::class, 'serviceCategoryCity'])->name('service-category.city');
Route::get('/hizmet/{slug}', [PublicController::class, 'serviceCategory'])->name('service-category.show');
Route::get('/ilan/{slug}', [PublicController::class, 'listing'])->name('listing.show');
Route::get('/sehir/{slug}', [PublicController::class, 'cityPage'])->name('city-page.show');
Route::get('/sayfa/{slug}', [PublicController::class, 'page'])->name('page.show');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('seo.robots');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/giris', [PortalAuthController::class, 'showLogin'])->name('auth.login');
Route::get('/kayit', [PortalAuthController::class, 'showRegister'])->name('auth.register');
Route::post('/giris', [PortalAuthController::class, 'login'])->middleware('throttle:admin')->name('auth.login.attempt');
Route::post('/kayit', [PortalAuthController::class, 'register'])->middleware('throttle:admin')->name('auth.register.submit');
Route::post('/cikis', [PortalAuthController::class, 'logout'])->name('auth.logout');
Route::get('/panel', [PortalAuthController::class, 'panel'])->middleware('admin.basic')->name('auth.panel');
Route::get('/hesabim', [PortalAuthController::class, 'account'])->middleware('admin.basic')->name('auth.account');
Route::get('/messages', [MessageCenterController::class, 'index'])->middleware(['admin.basic', 'ability:customer.message.view'])->name('messages.index');
Route::post('/messages/reply', [MessageCenterController::class, 'reply'])->middleware(['admin.basic', 'ability:customer.message.view'])->name('messages.reply');
Route::post('/messages/bulk', [MessageCenterController::class, 'bulk'])->middleware(['admin.basic', 'ability:customer.message.view'])->name('messages.bulk');
Route::get('/messages/thread', [MessageCenterController::class, 'thread'])->middleware(['admin.basic', 'ability:customer.message.view'])->name('messages.thread');
Route::post('/hesabim/profil', [PortalAuthController::class, 'updateProfile'])->middleware('admin.basic')->name('auth.account.profile');
Route::post('/hesabim/sifre', [PortalAuthController::class, 'updatePassword'])->middleware('admin.basic')->name('auth.account.password');
Route::redirect('/admin', '/admin/pages');

Route::prefix('admin')->name('admin.')->middleware(['admin.basic', 'throttle:admin', 'ability:admin.access'])->group(function () {
    Route::resource('pages', PageController::class)->except('show')->middleware('ability:pages.manage');
    Route::resource('categories', CategoryController::class)->except('show')->middleware('ability:categories.manage');
    Route::post('categories/bulk-update', [CategoryController::class, 'bulkUpdate'])->name('categories.bulk-update')->middleware('ability:categories.manage');
    Route::resource('categories.attributes', CategoryAttributeController::class)
        ->parameters(['attributes' => 'attribute'])
        ->except('show')
        ->names('category-attributes')
        ->middleware('ability:categories.manage');
    Route::resource('listings', ListingController::class)->except('show')->middleware('ability:listings.manage');
    Route::resource('city-pages', CityPageController::class)->except('show')->middleware('ability:city_pages.manage');
    Route::get('feedbacks', [AdminFeedbackController::class, 'index'])->name('feedback.index')->middleware('ability:admin.feedback.view');
    Route::post('feedbacks/{feedback}/comment-status', [AdminFeedbackController::class, 'updateCommentStatus'])->name('feedback.comment.status')->middleware('ability:admin.feedback.manage');
});

Route::prefix('owner')->name('owner.')->middleware(['admin.basic', 'ability:owner.access'])->group(function () {
    Route::get('/', [OwnerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/listings/create', [OwnerDashboardController::class, 'create'])->middleware('ability:owner.listings.manage')->name('listings.create');
    Route::post('/listings', [OwnerDashboardController::class, 'store'])->middleware('ability:owner.listings.manage')->name('listings.store');
    Route::get('/listings/{listing}/edit', [OwnerDashboardController::class, 'editListing'])->middleware(['ability:owner.listings.manage', 'owner.owns:listing'])->name('listings.edit');
    Route::put('/listings/{listing}', [OwnerDashboardController::class, 'updateListing'])->middleware(['ability:owner.listings.manage', 'owner.owns:listing'])->name('listings.update');
    Route::post('/listings/{listing}/status', [OwnerDashboardController::class, 'updateListingStatus'])->middleware(['ability:owner.listings.manage', 'owner.owns:listing'])->name('listings.status');
    Route::get('/listings', [OwnerDashboardController::class, 'listings'])->middleware('ability:owner.listings.manage')->name('listings.index');
    Route::get('/leads', [OwnerDashboardController::class, 'leads'])->middleware('ability:owner.leads.view')->name('leads.index');
    Route::get('/settings', [OwnerDashboardController::class, 'settings'])->name('settings');
    Route::post('/leads/{customerRequest}/status', [OwnerDashboardController::class, 'updateLeadStatus'])->middleware(['ability:owner.leads.manage', 'owner.owns:lead'])->name('leads.status');
    Route::get('/feedbacks', [OwnerFeedbackController::class, 'index'])->middleware('ability:owner.feedback.view')->name('feedback.index');
    Route::post('/feedbacks/bulk', [OwnerFeedbackController::class, 'bulkUpdate'])->middleware('ability:owner.feedback.manage')->name('feedback.bulk');
    Route::post('/feedbacks/{feedback}/status', [OwnerFeedbackController::class, 'updateStatus'])->middleware(['ability:owner.feedback.manage', 'owner.owns:feedback'])->name('feedback.status');
});

Route::prefix('customer')->name('customer.')->middleware(['admin.basic', 'ability:customer.access'])->group(function () {
    Route::get('/', [CustomerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/requests', [CustomerDashboardController::class, 'requests'])->middleware('ability:customer.requests.view')->name('requests.index');
    Route::post('/requests', [CustomerDashboardController::class, 'store'])->middleware('ability:customer.requests.create')->name('requests.store');
    Route::redirect('/messages', '/messages')->middleware('ability:customer.message.view')->name('messages.index');
    Route::redirect('/feedbacks', '/hesabim?tab=comments')->middleware('ability:customer.feedback.view')->name('feedback.index');
    Route::post('/feedbacks', [CustomerFeedbackController::class, 'store'])->middleware('ability:customer.feedback.create')->name('feedback.store');
    Route::post('/feedbacks/like', [CustomerFeedbackController::class, 'like'])->middleware('ability:customer.feedback.create')->name('feedback.like');
});

Route::prefix('support')->name('support.')->middleware(['admin.basic', 'ability:support.access'])->group(function () {
    Route::get('/', [SupportDashboardController::class, 'index'])->name('dashboard');
    Route::get('/requests', [SupportDashboardController::class, 'requests'])->middleware('ability:support.requests.view')->name('requests.index');
    Route::post('/requests/{customerRequest}/status', [SupportDashboardController::class, 'updateRequestStatus'])->middleware('ability:support.requests.manage')->name('requests.status');
});

Route::fallback(static function () {
    abort(404);
});
