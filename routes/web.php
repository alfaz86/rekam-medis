<?php

use App\Http\Controllers\MedicalRecordReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/print-records', [MedicalRecordReportController::class, 'print'])->name('print.records');
