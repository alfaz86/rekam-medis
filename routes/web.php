<?php

use App\Filament\Resources\MedicalRecordResource;
use App\Filament\Resources\PatientResource;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/print/medical-record-reports', [ReportController::class, 'printMedicalRecordReport'])->name('print.medical-record-reports');
Route::get('/print/patient-reports', [ReportController::class, 'printPatientReport'])->name('print.patient-reports');
Route::get('/print/patient/{id}', [PatientResource::class, 'printPatient'])->name('print.patient');
Route::get('/print/patients', [PatientResource::class, 'printPatients'])->name('print.patients');
Route::get('/print/medical-record/{id}', [MedicalRecordResource::class, 'printMedicalRecord'])->name('print.medical-record');
Route::get('/print/medical-records', [MedicalRecordResource::class, 'printMedicalRecords'])->name('print.medical-records');
