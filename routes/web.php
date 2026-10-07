<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

// Bosh sahifa — welcome
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('home');

// Auth routes
Auth::routes(['register' => false]);

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

    // /home → /dashboard (eski havolalar uchun)
    Route::get('/home', function () {
        return redirect()->route('dashboard');
    })->name('home.redirect');

    // Davomat
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/',                  'AttendanceController@today')->name('today');
        Route::post('/',                 'AttendanceController@store')->name('store');
        Route::get('/alerts',            'AttendanceController@alerts')->name('alerts');
        Route::get('/student/{student}', 'AttendanceController@studentHistory')->name('student');
    });

    // Xavf holatlari
    Route::get('/cases',                'RiskCaseController@index')->name('cases.index');
    Route::get('/cases/create',         'RiskCaseController@create')->name('cases.create');
    Route::post('/cases',               'RiskCaseController@store')->name('cases.store');
    Route::get('/cases/{case}',         'RiskCaseController@show')->name('cases.show');
    Route::patch('/cases/{case}/status','RiskCaseController@updateStatus')->name('cases.status');

    // O'quvchilar
    Route::get('/students',             'StudentController@index')->name('students.index');
    Route::post('/students',            'StudentController@store')->name('students.store');
    Route::get('/students/{student}',   'StudentController@show')->name('students.show');
    Route::get('/profile',           'ProfileController@index')->name('profile');
Route::get('/profile/edit',      'ProfileController@edit')->name('profile.edit');
Route::put('/profile',           'ProfileController@update')->name('profile.update');
Route::put('/profile/password',  'ProfileController@updatePassword')->name('profile.password');
});
Route::get('/attendance/absent', 'AttendanceController@absentToday')->name('attendance.absent');
Route::get('/statistics', 'StatisticsController@index')->name('statistics');
Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/',              'SettingsController@index')->name('index');
    Route::put('/profile',       'SettingsController@updateProfile')->name('profile');
    Route::put('/password',      'SettingsController@updatePassword')->name('password');
    Route::put('/notifications', 'SettingsController@updateNotifications')->name('notifications');
    Route::put('/system',        'SettingsController@updateSystem')->name('system');
});
 Route::prefix('parent')->name('parent.')->group(function () {
        Route::get('/dashboard',      'ParentController@dashboard')->name('dashboard');
        Route::get('/notifications',  'ParentController@notifications')->name('notifications');
        Route::get('/profile',        'ParentController@profile')->name('profile');
        Route::get('/child/{student}','ParentController@childDetail')->name('child');
    });


    // ==================== ALOQA (public) ====================
Route::post('/contact', 'ContactController@send')->name('contact.send');

// ==================== ADMIN XABARLAR ====================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/messages',                   'Admin\MessageController@index')->name('messages.index');
    Route::get('/messages/{message}',         'Admin\MessageController@show')->name('messages.show');
    Route::patch('/messages/{message}/status','Admin\MessageController@updateStatus')->name('messages.status');
    Route::patch('/messages/{message}/star',  'Admin\MessageController@toggleStar')->name('messages.star');
    Route::patch('/messages/{message}/note',  'Admin\MessageController@updateNote')->name('messages.note');
    Route::delete('/messages/{message}',      'Admin\MessageController@destroy')->name('messages.destroy');
});
Route::get('/students',              'StudentController@index')->name('students.index');
Route::post('/students',             'StudentController@store')->name('students.store');
Route::get('/students/{student}',    'StudentController@show')->name('students.show');
Route::get('/students/{student}/edit','StudentController@edit')->name('students.edit');
Route::put('/students/{student}',    'StudentController@update')->name('students.update');
Route::delete('/students/{student}', 'StudentController@destroy')->name('students.destroy');

// ==================== TIL ALMASHTIRISH ====================
Route::get('/language/{locale}', function ($locale) {
    $available = array_keys(config('languages.available', []));
    if (in_array($locale, $available)) {
        session(['locale' => $locale]);
        if (auth()->check()) {
            auth()->user()->update(['language' => $locale]);
        }
    }
    return redirect()->back();
})->name('language.switch');