<?php

use App\Http\Controllers\NoteController;
use Illuminate\Support\Facades\Route;

// Route API untuk CRUD catatan (index, store, destroy)
Route::apiResource('notes', NoteController::class)->only(['index', 'store', 'destroy']);
