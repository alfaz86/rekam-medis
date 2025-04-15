<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekam Medis</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 4px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f2f2f2;
        }

        .container {
            margin: auto;
        }

        .print-div {
            width: 100%;
            display: none;
            text-align: center;
            margin: 16px 0;
        }

        .print-div button {
            width: 100%;
            background-color: #D97706;
            color: white;
            border: none;
            padding: 10px 16px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
        }

        .print-div button:hover {
            background-color: #F59E0B;
        }

        @media print {
            thead {
                display: table-row-group;
            }

            .print-div {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="print-div" id="printButton">
        <button onclick="window.print()">Cetak Halaman</button>
    </div>

    <div class="container">
        <h2>Laporan Rekam Medis</h2>

        <table>
            <thead>
                <tr>
                    <th>Pasien</th>
                    <th>Bidan</th>
                    <th>Pemeriksaan</th>
                    <th>Tindakan Medis</th>
                    <th>Konsultasi dan Tindak Lanjut</th>
                    <th style="min-width: 82px !important;">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $record)
                <tr>
                    <td>{{ $record->patient->name }}</td>
                    <td>{{ $record->handledBy->name }}</td>
                    <td>
                        <strong>Riwayat Kesehatan:</strong><br>
                        {!! nl2br(e($record->medical_history)) !!}<br><br>

                        <strong>Keluhan:</strong><br>
                        {!! nl2br(e($record->complaint)) !!}<br><br>

                        <strong>Hasil Pemeriksaan:</strong><br>
                        {!! nl2br(e($record->examination_results)) !!}<br><br>

                        <strong>Diagnosis:</strong><br>
                        {!! nl2br(e($record->diagnosis)) !!}
                    </td>
                    <td>
                        {!! nl2br(e($record->medical_treatment)) !!}<br><br>
                        <strong>Obat:</strong><br>
                        @forelse($record->medicineUsages as $usage)
                            - {{ $usage->medicine->name }} | {{ $usage->usage }}<br>
                        @empty
                            -
                        @endforelse
                    </td>
                    <td>{!! nl2br(e($record->consultation_and_follow_up)) !!}</td>
                    <td>{{ $record->created_at->format('d-m-Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        const isDesktop = window.innerWidth >= 768 && !/Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent);

        if (isDesktop) {
            window.print();
            window.onafterprint = function() {
                window.close();
            };
        } else {
            document.getElementById('printButton').style.display = 'block';
        }
    </script>
</body>
</html>
