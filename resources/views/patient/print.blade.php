<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Kartu Pasien</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .kop-surat-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .kop-surat {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .kop-surat img {
            width: 54px;
        }
        .kop-surat-text {
            text-align: center;
        }
        .kop-surat-text p {
            margin: 0;
            padding: 0;
        }
        .kop-surat-text p.bold {
            font-size: 20px;
            text-decoration: underline;
            font-weight: bold;
        }
        .kop-surat-text span {
            font-size: 12px;
            display: block;
            margin: 0;
            padding: 0;
        }
        .separator {
            border-bottom: 2px solid black;
            margin: 10px 0 20px;
        }
        .patient-section {
            margin-bottom: 40px;
            page-break-inside: avoid; /* Hindari pemotongan di tengah halaman */
        }
        .patient-info {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .patient-info th,
        .patient-info td {
            padding: 8px;
            text-align: left;
        }
        .patient-info th {
            width: 80px;
        }
        .patient-info th:nth-child(2),
        .patient-info td:nth-child(2) {
            width: 1px;
        }
        .underline {
            display: inline-block;
            width: 100%;
            border-bottom: 1px dashed black;
            padding-left: 5px;
        }
        .patient-footer {
            text-align: center;
            margin-top: 50px;
            font-weight: bold;
        }
        @media print {
            .patient-section:last-child {
                page-break-after: auto;
            }
        }
    </style>
</head>
<body>
    <div class="content">
        @foreach ($patients as $patient)
            <div class="patient-section">
                <!-- Kop surat -->
                <div class="kop-surat-wrapper">
                    <div class="kop-surat">
                        <img src="{{ asset('images/logo-IDI.png') }}" alt="Logo">
                        <div class="kop-surat-text">
                            <p>{{ $letterhead['title'] }}</p>
                            <p class="bold">{{ $letterhead['name'] }}</p>
                            @foreach ($letterhead['address'] as $item)
                                <span>{{ $item }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="separator"></div>

                <!-- Tabel Data Pasien -->
                <table class="patient-info">
                    <tr>
                        <th colspan="3" style="text-align: center;">KARTU BEROBAT</th>
                    </tr>
                    <tr>
                        <th>No. Regis</th>
                        <th>:</th>
                        <td class="underline">{{ $patient->number_identity }}</td>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <th>:</th>
                        <td class="underline">{{ $patient->name }}</td>
                    </tr>
                    <tr>
                        <th>Alamat</th>
                        <th>:</th>
                        <td class="underline">{{ $patient->address }}</td>
                    </tr>
                </table>
            </div>
            
            <!-- Footer -->
            <div class="patient-footer">
                <p>KARTU HARAP DIBAWA SETIAP BEROBAT</p>
            </div>
        @endforeach
    </div>
    <script>
        window.onload = function() {
            window.print();
            window.onafterprint = function() {
                window.close();
            };
        };
    </script>
</body>
</html>
