<?php
use Illuminate\Support\Facades\Route;
Route::get('/test-workflows', function () {
    return \App\Models\Workflow::with('steps')->get();
});
