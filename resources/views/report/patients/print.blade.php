<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pasien</title>
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
        <h2>Laporan Pasien</h2>

        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Tanggal Lahir</th>
                    <th>Nama Suami</th>
                    <th>Alamat</th>
                    <th>No HP</th>
                    <th>Tanggal Daftar</th>
                </tr>
            </thead>
            <tbody>
                @foreach($patients as $record)
                <tr>
                    <td>{{ $record->name }}</td>
                    <td>{{ $record->birth_date ? \Carbon\Carbon::parse($record->birth_date)->format('d-m-Y') : '-' }}</td>
                    <td>{{ $record->husband_name ?? '-' }}</td>
                    <td>{{ $record->address }}</td>
                    <td>{{ $record->phone_number }}</td>
                    <td>{{ $record->created_at ? \Carbon\Carbon::parse($record->created_at)->format('d-m-Y') : '-' }}</td>
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
