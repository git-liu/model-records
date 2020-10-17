<?php


use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('ModifyRecord\\Controllers')->prefix('api')->middleware('auth:api')->group(function () {
    Route::get('/records', 'RecordController@index')->name('records/index');
    
    // 测试路由
    Route::post('/records/test', 'RecordController@test')->name('records/test');
});
