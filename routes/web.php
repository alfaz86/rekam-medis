<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/print/medical-record-reports', [ReportController::class, 'printMedicalRecordReport'])->name('print.medical-record-reports');
Route::get('/print/patient-reports', [ReportController::class, 'printPatientReport'])->name('print.patient-reports');
