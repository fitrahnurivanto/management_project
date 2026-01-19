<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PKS - {{ $pks_number }}</title>
    <style>
        @page {
            margin: 1.5cm 2cm 1cm 2cm; /* Reduced bottom margin */
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.05;
            color: #000;
        }
        .logo-header {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .logo-left {
            display: table-cell;
            width: 30%;
            text-align: left;
            vertical-align: middle;
        }
        .logo-left img {
            height: 1.16cm;
            width: 4.29cm;
        }
        .logo-right {
            display: table-cell;
            width: 30%;
            text-align: right;
            vertical-align: middle;
        }
        .logo-right img {
            height: 1.5cm;
            width: auto;
        }
        .logo-center {
            display: table-cell;
            width: 40%;
        }
        .header {
            text-align: center;
            margin-bottom: 12pt;
        }
        .header h1 {
            font-size: 10pt;
            font-weight: bold;
            margin: 3pt 0;
            text-transform: uppercase;
            line-height: 1.2;
        }
        .header p {
            margin: 3pt 0;
            font-size: 10pt;
            text-decoration: underline;
        }
        .content {
            text-align: justify;
            margin-bottom: 8pt;
        }
        .content p {
            margin-bottom: 4pt;
            text-indent: 1cm;
        }
        .content p.no-indent {
            text-indent: 0;
        }
        .content p.left-align {
            text-align: left;
            text-indent: 0;
        }
        .party-info {
            margin-left: 1cm;
            margin-bottom: 4pt;
        }
        .party-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .party-info td {
            padding: 1pt 0;
            vertical-align: top;
            border: none;
        }
        .party-info td:first-child {
            width: 100px;
        }
        .party-info td:nth-child(2) {
            width: 20px;
            text-align: center;
        }
        .terms {
            margin-left: 0.5cm;
            margin-bottom: 6pt;
        }
        .terms ol {
            padding-left: 1cm;
            margin: 0;
        }
        .terms li {
            margin-bottom: 2pt;
            text-align: justify;
        }
        .signature {
            margin-top: 12pt;
            page-break-inside: avoid;
        }
        .signature table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 0 20px;
            border: none;
        }
        .signature .title {
            font-weight: bold;
            margin-bottom: 40px;
        }
        .signature .name {
            font-weight: bold;
            text-decoration: underline;
        }
        .signature .position {
            font-style: italic;
        }
        .bold {
            font-weight: bold;
        }
        .closing {
            text-align: center;
            margin-bottom: 12pt;
            margin-top: 4pt;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Logo Header -->
    <div class="logo-header">
        <div class="logo-left">
            @if($company_logo)
                @if(file_exists($company_logo))
                    <img src="{{ $company_logo }}" alt="Creativemu Logo">
                @else
                    <!-- Debug: Logo not found at {{ $company_logo }} -->
                @endif
            @else
                <!-- Debug: Company logo variable is empty -->
            @endif
        </div>
        <div class="logo-center"></div>
        <div class="logo-right">
            @if($client_logo)
                @if(file_exists($client_logo))
                    <img src="{{ $client_logo }}" alt="Client Logo">
                @else
                    <!-- Debug: Client logo not found at {{ $client_logo }} -->
                @endif
            @else
                <!-- Debug: Client logo variable is empty -->
            @endif
        </div>
    </div>

    <div class="header">
        <h1>Perjanjian Kerjasama Antara</h1>
        <h1>{{ strtoupper($company_name) }} dengan {{ strtoupper($client_name) }}</h1>
        <p>Nomor : {{ $pks_number }}</p>
    </div>

    <div class="content">
        <p class="no-indent">Kami yang bertanda tangan dibawah ini:</p>

        <div class="party-info">
            <table>
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td>{{ $company_director }}</td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>:</td>
                    <td>Direktur {{ $company_name }}</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ $company_address }}</td>
                </tr>
            </table>
        </div>

        <p class="left-align">Selanjutnya disebut sebagai <span class="bold">pihak pertama</span>.</p>

        <div class="party-info">
            <table>
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td>{{ $client_name }}</td>
                </tr>
                <tr>
                    <td>Jabatan</td>
                    <td>:</td>
                    <td>{{ $client_position }}</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td>{{ $client_address }}</td>
                </tr>
            </table>
        </div>

        <p class="left-align">Selanjutnya disebut sebagai <span class="bold">pihak kedua</span>.</p>

        <p>Pada hari <span class="bold">{{ $pks_date->isoFormat('dddd') }}</span> tanggal <span class="bold">{{ $pks_date->day }} bulan {{ $pks_date->isoFormat('MMMM') }} {{ $pks_date->year }}</span> kedua belah pihak sepakat bahwa akan melakukan kerjasama Pekerjaan Pengelolaan Website dengan isi perjanjian sebagai berikut:</p>

        <div class="terms">
            <ol>
                <li><span class="bold">Pihak Pertama</span> melaksanakan Pekerjaan {{ $service_description }}.</li>
                
                <li><span class="bold">Pihak Pertama</span> melaksanakan Pekerjaan Pengelolaan Website dalam jangka waktu {{ $duration }} terhitung sejak pembayaran dilakukan.</li>
                
                <li><span class="bold">Pihak Pertama</span> akan memberikan laporan hasil pekerjaan pada <span class="bold">Pihak Kedua</span> setelah pekerjaan selesai.</li>
                
                <li><span class="bold">Pihak Kedua</span> akan memberikan informasi yang diperlukan dalam pelaksanaan pekerjaan.</li>
                
                <li><span class="bold">Pihak Kedua</span> melakukan pembayaran 1 (satu) termin kepada <span class="bold">Pihak Pertama</span> sebesar Rp {{ number_format($payment_amount, 0, ',', '.') }},-, belum termasuk Pph 23.</li>
                
                <li>Apabila terdapat perselisihan selama proses, sebelum dan setelah dilaksanakannya pekerjaan maka kedua belah pihak sepakat untuk menyelesaikan permasalahan secara kekeluargaan terlebih dahulu. Apabila tidak ditemukan solusi atas permasalahan maka kedua belah pihak sepakat untuk menyelesaikan permasalahan melalui jalur hukum.</li>
            </ol>
        </div>

        <p>Demikian Perjanjian Kerjasama ini kami buat sebagai pengikat dalam kerjasama yang terjalin.</p>
    </div>

    <div class="closing">
        {{ $signing_location }}, {{ $pks_date->isoFormat('D MMMM Y') }}
    </div>

    <div class="signature">
        <table>
            <tr>
                <td>
                    <div class="title">PIHAK PERTAMA<br><br></div>
                    <div class="name">{{ $company_director }}</div>
                    <div class="position">Direktur</div>
                </td>
                <td>
                    <div class="title">PIHAK KEDUA<br><br></div>
                    <div class="name">{{ $client_name }}</div>
                    <div class="position">{{ $client_position }}</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
