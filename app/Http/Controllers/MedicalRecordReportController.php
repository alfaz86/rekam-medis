<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use Illuminate\Http\Request;

class MedicalRecordReportController extends Controller
{
    public function print(Request $request)
    {
        $query = MedicalRecord::query()->with(['patient', 'handledBy', 'room', 'medicines']);
        $filters = $request->input('filters', []);

        if (!empty($filters['patient_id'])) {
            $query->where('patient_id', $filters['patient_id']);
        }
        if (!empty($filters['handled_by_id'])) {
            $handledBy = explode(':', $filters['handled_by_id']['value']);
            $query->where('handled_by_type', $handledBy[0])
                ->where('handled_by_id', $handledBy[1]);
        }
        if (!empty($filters['room_id'])) {
            $query->where('room_id', $filters['room_id']);
        }
        if (!empty($filters['date']['date_from']) && !empty($filters['date']['date_until'])) {
            $query->whereBetween('date', [$filters['date']['date_from'], $filters['date']['date_until']]);
        }

        $records = $query->get();

        return view('medical-records.print', compact('records'));
    }
}
