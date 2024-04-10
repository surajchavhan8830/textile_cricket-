<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\YarnController;
use App\Http\Controllers\YarnCategoryController;
use App\Http\Controllers\FabricCategoryController;
use App\Http\Controllers\FabricCostController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\user\UserController;
use App\Models\FabricCost;
use App\Models\Package;
use App\Models\Packagelist;
use Carbon\Carbon;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Models\User;




// Route::get('/', function () {
//     return view('welcome');
// })->name('welcome');

Route::get('/', function () {
    return view('auth.login');
});

Route::get('signin', [AuthController::class, 'signin'])->name('signin');
Route::post('signin', [AuthController::class, 'singin_create'])->name('singin_create');

Route::post('Weblogin', [AuthController::class, 'Weblogin'])->name('Weblogin');

Route::middleware(['security'])->group(function () {
    // login 


    Route::get('/logout', function () {
        if (session()->has('name')) {
            // session()->pull('name');
            session()->forget('name');
            session()->flush();
        }
        return redirect('/')->with('message', 'logout Successfully!');
    })->name('logout');


    Route::get('dashboard', function () {

        $user_id =  session('id');
        // dd(session('is_customer'));
        if (session('is_customer') == 0) {
            return view('dashboard.index');
        } else {
            $package = Package::where('user_id', $user_id)->first();

            $endDate = $package->ending_date;
            $currentDate = Carbon::now();

            $daysRemaining = $currentDate->diffInDays($endDate);
            // dd($daysRemaining );

            return view('dashboard.index', compact('daysRemaining'));
        }
    })->name('dashboard');

    // Yarn Details Route
    Route::resource('yarn', YarnController::class);

    // Yarn Category
    Route::resource('yarn_category', YarnCategoryController::class);

    // Fabric Route
    Route::resource('fabric_category', FabricCategoryController::class);

    // Fabric Cost
    Route::resource('fabricCost', FabricCostController::class);
    Route::get('warp_yarnCreate', [FabricCostController::class, 'warp_yarnCreate'])->name('fabricCost.warp_yarnCreate');
    Route::post('warp_yarnStore', [FabricCostController::class, 'warp_yarnStore'])->name('fabricCost.warp_yarnStore');
    Route::post('AddWarp', [FabricCostController::class, 'AddWarp'])->name('fabricCost.added');

    // Customer 
    Route::resource('customer', CustomerController::class);

    // Packagelist
    Route::resource('packagelist', PackageController::class);
    Route::get('users_package/{id}', [PackageController::class, 'user_package'])->name('packagelist.user_package');
});

Route::get('admin_login', [UserController::class, 'admin_login'])->name('admin_login');

Route::post('admin_login', [UserController::class, 'admin_login_form'])->name('admin_login_form');


Route::get('user_customer', [UserController::class, 'user_customer'])->name('user_customer');
Route::get('inactive', [UserController::class, 'inactive'])->name('inactive');

// Route for user view
Route::get('user_package', [UserController::class, 'user_package'])->name('user_package');
Route::get('users_package/{id}', [UserController::class, 'user_pack'])->name('package_single');
Route::get('create_package/{id}', [UserController::class, 'create_package'])->name('create_package');
Route::get('edit_package/{id}', [UserController::class, 'edit_package'])->name('edit_package');
Route::post('store_package', [UserController::class, 'store_package'])->name('store_package');
Route::post('update_package/{id}', [UserController::class, 'update_package'])->name('update_package');



Route::get('/logoutadmin', function () {
    if (session()->has('name')) {
        // session()->pull('name');
        session()->forget('name');
        session()->flush();
    }
    return redirect('admin_login')->with('message', 'logout Successfully!');
})->name('logoutadmin');
