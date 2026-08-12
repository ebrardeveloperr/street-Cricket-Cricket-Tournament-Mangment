<?php

use App\Http\Controllers\Api\DeliveryApiController;
use App\Http\Controllers\Api\InningApiController;
use App\Http\Controllers\Api\MatchApiController;
use App\Http\Controllers\Api\PlayerApiController;
use App\Http\Controllers\Api\TeamApiController;
use App\Http\Controllers\Api\TournamentApiController;
use Illuminate\Support\Facades\Route;

Route::get('/teams', [TeamApiController::class, 'index']);
Route::get('/players', [PlayerApiController::class, 'index']);
Route::get('/tournaments', [TournamentApiController::class, 'index']);

Route::get('/matches', [MatchApiController::class, 'index']);
Route::post('/matches', [MatchApiController::class, 'store']);
Route::patch('/matches/{match}/complete', [MatchApiController::class, 'complete']);
Route::get('/matches/{match}/scorecard', [MatchApiController::class, 'scorecard']);

Route::post('/innings', [InningApiController::class, 'store']);
Route::patch('/innings/{inning}', [InningApiController::class, 'update']);

Route::post('/deliveries', [DeliveryApiController::class, 'store']);
Route::delete('/innings/{inning}/deliveries/latest', [DeliveryApiController::class, 'destroyLatest']);
