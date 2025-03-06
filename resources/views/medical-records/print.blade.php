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
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .container {
            margin: auto;
        }

    </style>
</head>
<body>

    <div class="container">
        <h2>Laporan Rekam Medis</h2>

        <table>
            <thead>
                <tr>
                    <th>Pasien</th>
                    <th>Keluhan</th>
                    <th>PCP</th>
                    <th>Diagnosis</th>
                    <th>Obat</th>
                    <th>Ruangan</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $record)
                <tr>
                    <td>{{ $record->patient->name }}</td>
                    <td>{{ $record->complaint }}</td>
                    <td>{{ $record->handledBy->name }}</td>
                    <td>{{ $record->diagnosis }}</td>
                    <td>
                        @if($record->medicines->isNotEmpty())
                        <ul style="margin: 0; padding-left: 16px;">
                            @foreach($record->medicines as $medication)
                            <li>{{ $medication->name }}</li>
                            @endforeach
                        </ul>
                        @else
                        <span>Tidak ada obat</span>
                        @endif
                    </td>
                    <td>{{ $record->room->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($record->date)->format('d-m-Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        window.print();

        window.onafterprint = function() {
            window.close();
        }
    </script>
</body>
</html>
