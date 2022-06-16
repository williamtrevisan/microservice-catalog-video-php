<?php

use App\Http\Controllers\Api\CastMemberController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\GenreController;
use Illuminate\Support\Facades\Route;

Route::apiResource('/cast_members', CastMemberController::class);
Route::apiResource('/categories', CategoryController::class);
Route::apiResource('/genres', GenreController::class);
Route::get('/', function() {
    return response()->json(['message' => 'success']);
});
