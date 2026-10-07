<?php
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ==================== AUTH ====================
    Route::post('/login',    'Api\AuthController@login');
    Route::post('/register', 'Api\AuthController@register');
    Route::post('/logout',   'Api\AuthController@logout')->middleware('auth:sanctum');

    // ==================== HIMOYALANGAN ====================
    Route::middleware('auth:sanctum')->group(function () {

        // O'quvchilar
        Route::apiResource('students', 'Api\StudentController');

        // Davomat
        Route::get('/attendance',         'Api\AttendanceController@index');
        Route::post('/attendance',        'Api\AttendanceController@store');

        // Xavf holatlari
        Route::apiResource('cases', 'Api\RiskCaseController');

        // Statistika
        Route::get('/statistics',         'Api\StatisticsController@index');

        // Xabarlar
        Route::get('/messages',           'Api\MessageController@index');
        Route::post('/messages',          'Api\MessageController@store');
    });
});