<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function printMedicalRecordReport(Request $request)
    {
        $query = MedicalRecord::query()->with([
            'patient' => function ($query) {
                $query->withTrashed();
            },
            'handledBy' => function ($query) {
                $query->withTrashed();
            },
            'room',
            'medicineUsages.medicine' => function ($query) {
                $query->withTrashed();
            },
        ]);
        $filters = $request->input('filters', []);

        if (!empty($filters['date']['date_from']) && !empty($filters['date']['date_until'])) {
            $query->whereBetween('date', [$filters['date']['date_from'], $filters['date']['date_until']]);
        }

        $records = $query->get();

        return view('report.medical-records.print', compact('records'));
    }

    public function printPatientReport(Request $request)
    {
        $query = Patient::query()->with(['user']);
        $filters = $request->input('filters', []);

        if (!empty($filters['date']['date_from']) && !empty($filters['date']['date_until'])) {
            $query->whereBetween('created_at', [
                $filters['date']['date_from'] . ' 00:00:00',
                $filters['date']['date_until'] . ' 23:59:59',
            ]);
        }

        $patients = $query->get();

        return view('report.patients.print', compact('patients'));
    }
}
