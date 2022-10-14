<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\ZipCode;

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

Route::match(['get'], '/zip-codes/{zip_code}', function ($zipCode) {
    //
    return ZipCode::getByZipCode($zipCode);
});
