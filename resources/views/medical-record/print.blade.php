<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Rekam Medis</title>
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
        .medical-medicalRecord-section {
            margin-bottom: 40px;
        }
        .medical-medicalRecord-info {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .medical-medicalRecord-info th,
        .medical-medicalRecord-info td {
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        .medical-medicalRecord-info th {
            width: 150px;
        }
        .medical-medicalRecord-info th:nth-child(2),
        .medical-medicalRecord-info td:nth-child(2) {
            width: 1px;
        }
        .underline {
            display: inline-block;
            width: 100%;
            border-bottom: 1px dashed black;
            padding-left: 5px;
        }
        #medicine th,
        #medicine td {
            border: 1px solid black;
            padding: 5px;
            text-align: left;
        }
        .print-div {
            width: 100%;
            display: none;
            text-align: center;
            margin: auto;
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
            margin: 16px 8px;
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

        @foreach ($medicalRecords as $medicalRecord)
            <div class="medical-medicalRecord-section">
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
                <table class="medical-medicalRecord-info">
                    <tr>
                        <th colspan="3" style="text-align: center;">REKAM MEDIS</th>
                    </tr>
                    <tr>
                        <th>Pasien</th>
                        <th>:</th>
                        <td class="underline">{{ $medicalRecord->patient->name }}</td>
                    </tr>
                    <tr>
                        <th>Bidan</th>
                        <th>:</th>
                        <td class="underline">{{ $medicalRecord->handledBy->name }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <th>:</th>
                        <td class="underline">{{ $medicalRecord->created_at->format('d-m-Y') }}</td>
                    </tr>
                    </tr>
                    <tr>
                        <th>Riwayat Kesehatan</th>
                        <th>:</th>
                        <td class="underline">{!! nl2br(e($medicalRecord->medical_history)) !!}</td>
                    </tr>
                    <tr>
                        <th>Keluhan</th>
                        <th>:</th>
                        <td class="underline">{!! nl2br(e($medicalRecord->complaint)) !!}</td>
                    </tr>
                    <tr>
                        <th>Hasil Pemeriksaan</th>
                        <th>:</th>
                        <td class="underline">{!! nl2br(e($medicalRecord->examination_results)) !!}</td>
                    </tr>
                    <tr>
                        <th>Diagnosis</th>
                        <th>:</th>
                        <td class="underline">{!! nl2br(e($medicalRecord->diagnosis)) !!}</td>
                    </tr>
                    <tr>
                        <th>Tindakan Medis</th>
                        <th>:</th>
                        <td class="underline">{!! nl2br(e($medicalRecord->medical_treatment)) !!}</td>
                    </tr>
                    <tr>
                        <th>Konsultasi dan Tindak Lanjut</th>
                        <th>:</th>
                        <td class="underline">{!! nl2br(e($medicalRecord->consultation_and_follow_up)) !!}</td>
                    </tr>
                    <tr>
                        <th>Obat</th>
                        <th>:</th>
                        <td>
                            <table id="medicine" style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th style="width: 40% !important;">Nama Obat</th>
                                        <th style="width: 60% !important;">Aturan Pakai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($medicalRecord->medicineUsages as $index => $usage)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $usage->medicine->name }}</td>
                                            <td>{{ $usage->usage }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3">-</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        @endforeach
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
