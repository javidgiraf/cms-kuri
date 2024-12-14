<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\SchemeController;
use App\Http\Controllers\GoldrateController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\LogActivityController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransactionDetailController;
use App\Http\Controllers\SchemeSettingController;
use App\Http\Controllers\GoldDepositController;
use App\Http\Controllers\PasswordController;
use App\Models\SubscriptionHistory;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/clear-cache', function () {

    Artisan::call('optimize:clear');

    return response()->json(['success' => true, 'message' => 'All Cache is cleared']);

})->name('cache.clear');

Route::get('/linkstorage', function () {

    Artisan::call('storage:link');

    return "storage Linked";
});



Auth::routes();
Route::group(['middleware' => ['auth', 'permission']], function () {
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index']);
    Route::get('/scheme-chart-data', [App\Http\Controllers\HomeController::class, 'getSchemeChartData']);
    Route::resource('roles', RolesController::class);
    Route::resource('permissions', PermissionsController::class);
    Route::resource('admins', AdminController::class);
    Route::resource('users', UserController::class);
    Route::get('/change-password', [PasswordController::class, 'showChangePasswordForm'])->name('password.change');
    Route::put('/change-password', [PasswordController::class, 'updatePassword'])->name('password.update');
    Route::resource('schemes', SchemeController::class);
    Route::resource('goldrates', GoldrateController::class);
    Route::resource('countries', CountryController::class);
    Route::resource('states', StateController::class);
    Route::resource('districts', DistrictController::class);
    Route::resource('logactivities', LogActivityController::class);
    Route::resource('settings', SettingController::class);
    Route::resource('scheme-settings', SchemeSettingController::class);

    Route::get('get-states', [CountryController::class, 'getStates'])->name('countries.get-states');
    Route::get('get-districts', [StateController::class, 'getDistricts'])->name('states.get-districts');
    Route::get('get-user-subscriptions', [UserController::class, 'getUserSubscriptions'])->name('users.get-user-subscriptions');
    Route::get('subscriptionsExport', [UserController::class, 'subscriptionsExport'])->name('subscriptionsExport');
    Route::get('subscriptions', [UserController::class, 'userSubscriptions'])->name('subscriptions.index');
    Route::get('subscriptions-create', [UserController::class, 'subscriptionsCreate'])->name('subscriptions.create');
    Route::post('subscriptions-store', [UserController::class, 'subscriptionsStore'])->name('subscriptions.store');
    Route::get('subscriptions-edit/{id}', [UserController::class, 'subscriptionsEdit'])->name('subscriptions.edit');
    Route::put('subscriptions-update/{id}', [UserController::class, 'subscriptionsUpdate'])->name('subscriptions.update');
    Route::get('subscriptions-delete/{id}', [UserController::class, 'subscriptions'])->name('subscriptions.destroy');
    Route::get('current-plan-history', [UserController::class, 'currentPlanHistory'])->name('users.current-plan-history');
    Route::get('generate-random-number', [UserController::class, 'generateRandomNumber'])->name('users.generate-random-number');
    Route::get('edit-scheme-details/{user_subscription_id}/{user_id}/{scheme_id}', [UserController::class, 'editSchemeDetails'])->name('users.edit-scheme-details');
    Route::post('pay-deposit', [UserController::class, 'payDeposit'])->name('users.pay-deposit');
    Route::get('unpaid-list', [UserController::class, 'unPaidList'])->name('users.unpaid-list');
    Route::post('update-plan-status', [UserController::class, 'updatePlanStatus'])->name('users.update-plan-status');
    Route::get('get-plan-status', [UserController::class, 'getPlanStatus'])->name('users.get-plan-status');
    Route::get('fetch-success-deposit-by-order', [UserController::class, 'fetchSuccessDepositbyOrder'])->name('users.fetch-success-deposit-by-order');
    Route::post('save-transaction-details', [UserController::class, 'saveTransactionDetails'])->name('users.save-transaction-details');
    Route::post('fetch-transaction-details', [UserController::class, 'fetchTransactionDetails'])->name('users.fetch-transaction-details');
    Route::get('fetch-failed-deposit-by-order', [UserController::class, 'fetchFailedDepositByorder'])->name('users.fetch-failed-deposit-by-order');
    Route::post('save-failed-process-status', [UserController::class, 'saveFailedProcessStatus'])->name('users.save-failed-process-status');
    Route::get('deposits', [OrderController::class, 'index'])->name('deposits.index');
    Route::post('change-status', [UserController::class, 'changeStatus'])->name('users.change-status');
    Route::post('change-subscription-status', [UserController::class, 'changeSubscriptionStatus'])->name('change-subscription-status');
    Route::post('change-maturity-status', [UserController::class, 'changeMaturityStatus'])->name('change-maturity-status');
    Route::get('payments', [DepositController::class, 'index'])->name('payments.index');
    Route::get('get-user-subscriptions-list', [UserController::class, 'getUserSubscriptionsList'])->name('users.get-user-subscriptions-list');
    Route::get('accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::get('transaction-details', [TransactionDetailController::class, 'index'])->name('transaction-details.index');
    Route::get('fetch-transaction-details', [TransactionDetailController::class, 'fetchTransactionDetails'])->name('transaction-details.fetch-transaction-details');
});
