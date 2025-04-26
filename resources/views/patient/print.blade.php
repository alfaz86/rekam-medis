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
            width: 48px;
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
        .footer {
            text-align: center;
            margin-top: 50px;
            font-weight: bold;
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
                            <p>Praktik Dokter Umum</p>
                            <p class="bold">dr. ANDIKA BAYANGKARA</p>
                            <span>Winanegara Kel. Teluk Merbau RT.001 RW.003</span>
                            <span>Kecamatan Dayun Kabupaten Siak</span>
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
        @endforeach

    </div>

    <div class="footer">
        <p>KARTU HARAP DIBAWA SETIAP BEROBAT</p>
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
