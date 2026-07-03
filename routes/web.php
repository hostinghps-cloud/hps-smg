<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Controllers\ImportController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailController;

/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {

    return view('auth.login');

})->name('login');

Route::post('/login', function (Request $request) {

    $credentials = $request->only(
        'name',
        'password'
    );

    if (Auth::attempt($credentials)) {

        $request->session()->regenerate();

        return redirect('/')->with(
            'success',
            'Selamat datang di halaman ' . auth()->user()->name
        );
    }

    return back()->with(
        'error',
        'Email atau password salah'
    );

});

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

Route::post('/logout', function () {

    Auth::logout();

    request()->session()->invalidate();

    request()->session()->regenerateToken();

    return redirect('/login')->with(
        'success',
        'Berhasil logout'
    );

});

/*
|--------------------------------------------------------------------------
| AUTH USER
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get('/', [ImportController::class, 'dashboard']);

    /*
    |--------------------------------------------------------------------------
    | BULK EMAIL
    |--------------------------------------------------------------------------
    */

    Route::get('/bulk', [ImportController::class, 'bulk']);

    /*
    |--------------------------------------------------------------------------
    | EMAIL ACTION
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/email/preview',
        [EmailController::class, 'preview']
    )->name('email.preview');

    Route::post(
        '/email/send',
        [EmailController::class, 'send']
    )->name('email.send');

    /*
    |--------------------------------------------------------------------------
    | UPLOAD
    |--------------------------------------------------------------------------
    */

    Route::get('/upload', [ImportController::class, 'uploadPage']);

    Route::post('/import', [ImportController::class, 'import']);

    /*
    |--------------------------------------------------------------------------
    | FILTER & EXPORT
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/filter-prefix',
        [ImportController::class, 'filterPrefix']
    );

    Route::get(
        '/export-prefix',
        [ImportController::class, 'exportPrefix']
    );

    /*
    |--------------------------------------------------------------------------
    | RAW DATA
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/raw-data',
        [ImportController::class, 'rawData']
    );

    /*
    |--------------------------------------------------------------------------
    | PN MASTER
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/pn-master',
        [ImportController::class, 'pnMaster']
    );

     /*
    |--------------------------------------------------------------------------
    | TEMPLATE MASTER
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/template-master',
        [TemplateController::class, 'index']
    );

    Route::post(
        '/template-master/store',
        [TemplateController::class, 'store']
    );

    Route::post(
        '/template-master/update/{id}',
        [ImportController::class, 'updateTemplate']
    );

    Route::delete(
        '/template-master/delete/{id}',
        [ImportController::class, 'deleteTemplate']
    );

    /*
    |--------------------------------------------------------------------------
    | EMAIL MASTER
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/email-master',
        [MasterController::class, 'emailMaster']
    );

    Route::post(
        '/email-master/store',
        [MasterController::class, 'storeEmailMaster']
    );

    Route::post(
        '/email-master/update/{id}',
        [MasterController::class, 'updateEmailMaster']
    );

    Route::delete(
        '/email-master/delete/{id}',
        [MasterController::class, 'deleteEmailMaster']
    );

    // ==========================
// FOOTER MASTER
// ==========================

    Route::get(
        '/footer-master',
        [MasterController::class, 'footerMaster']
    );

    Route::post(
        '/footer-master/store',
        [MasterController::class, 'storeFooterMaster']
    );

    Route::put(
        '/footer-master/update/{id}',
        [MasterController::class, 'updateFooterMaster']
    );

    Route::delete(
        '/footer-master/delete/{id}',
        [MasterController::class, 'deleteFooterMaster']
    );

    /*
    |--------------------------------------------------------------------------
    | USER MASTER
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/user-master',
        [MasterController::class, 'userMaster']
    );

    Route::post(
        '/user-master/store',
        [MasterController::class, 'storeUserMaster']
    );

    Route::post(
        '/user-master/update/{id}',
        [MasterController::class, 'updateUserMaster']
    );

    Route::delete(
        '/user-master/delete/{id}',
        [MasterController::class, 'deleteUserMaster']
    );
    

});

/*
|--------------------------------------------------------------------------
| ADMIN ONLY
|--------------------------------------------------------------------------
*/



/*
|--------------------------------------------------------------------------
| TEST EMAIL
|--------------------------------------------------------------------------
*/

Route::get('/test-email', function () {

    Mail::raw(
        'SMTP berhasil digunakan',
        function ($message) {

            $message->to(
                'ce4.semarang@harmoniputra.com'
            )->subject(
                    'Test SMTP Laravel'
                );

        }
    );

    return 'Email berhasil dikirim';

});

Route::get(
    '/template/filter',
    [TemplateController::class, 'getTemplateByMonitoring']
);

Route::put(
    '/template-master/update/{id}',
    [TemplateController::class, 'update']
);
Route::get('/dashboard', [DashboardController::class, 'dashboard'])
    ->name('dashboard');
Route::get('/get-batches', [ImportController::class, 'getBatches']);