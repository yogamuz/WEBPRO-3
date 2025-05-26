<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daftar Antrian {{ $layanan->nama_layanan }} - {{ ucwords(strtolower($user->name)) }}
</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 2cm;
            line-height: 1.5;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 15px;
        }
        .header h2 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 10px;
        }
        .logo {
            max-height: 70px;
        }
        .info {
            text-align: center;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .user-info {
            text-align: center;
            margin-bottom: 20px;
            font-size: 16px;
            color: #2c3e50;
            font-weight: bold;
        }
        .date-info {
            text-align: right;
            margin-bottom: 20px;
            font-style: italic;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            table-layout: fixed;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
            word-wrap: break-word;
            vertical-align: top;
            font-size: 12px;
        }
        
        /* Khusus untuk kolom nomor antrian */
        .nomor-antrian {
            white-space: nowrap;
            text-align: center;
            font-weight: bold;
        }
        th {
            background-color: #2c3e50;
            color: white;
            font-weight: normal;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .date {
            margin-top: 40px;
            text-align: right;
        }
        .signature {
            margin-top: 80px;
            text-align: right;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-number {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
        }
        .empty-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-container">
            <!-- Jika Anda memiliki logo yang tersimpan -->
            <!-- <img src="data:image/png;base64,{{ $logo ?? '' }}" class="logo" alt="Logo"> -->
        </div>
        <h2>DAFTAR ANTRIAN {{ strtoupper($layanan->nama_layanan) }}</h2>
        <div class="info">
            ANTRIAN PELAYANAN KEPENDUDUKAN DAN CATATAN SIPIL
        </div>

    </div>
    
    <div class="date-info">
        Tanggal Cetak: {{ date('d F Y') }}
    </div>
    
    <table>
        <thead>
            <tr>
                <th width="8%">No</th>
                <th width="18%">Tgl. Antrian</th>
                <th width="25%">Nama Lengkap</th>
                <th width="19%">Nomor Antrian</th>
                <th width="30%">Alamat</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse ($antrians as $antrian)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ date('d/m/Y', strtotime($antrian->tanggal)) }}</td>
                    <td>{{ strtoupper( strtoupper($antrian->nama_lengkap)) }}</td>
                    <td class="nomor-antrian">{{ $antrian->kode }}</td>
                    <td>{{ $antrian->alamat }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="empty-data">Anda belum memiliki antrian untuk layanan ini</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="date">
        <p>Kota Depok, {{ date('d F Y') }}</p>
    </div>
    
    <div class="signature">
        <p>{{ strtoupper($user->name )}}</p>
        <br><br><br>
        <p><strong>_________________________</strong></p>
        <p>Tanda Tangan Pendaftar</p>
    </div>
    
    <div class="footer">
        Dokumen ini dicetak secara resmi melalui Antrian Resmi Disdukcapil Online Kota Depok .
    </div>
    
    <div class="page-number">
        Halaman 1
    </div>
</body>
</html>