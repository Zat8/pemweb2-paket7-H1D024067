<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Times New Roman', serif; margin: 0; padding: 0; }
        .certificate { width: 100%; height: 100vh; padding: 60px; box-sizing: border-box; text-align: center; border: 10px solid #1e3a8a; }
        .title { font-size: 36px; font-weight: bold; color: #1e3a8a; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 2px; }
        .subtitle { font-size: 18px; color: #555; margin-bottom: 40px; }
        .name { font-size: 32px; font-weight: bold; color: #000; margin: 20px 0; border-bottom: 2px solid #1e3a8a; display: inline-block; padding-bottom: 5px; }
        .event-title { font-size: 22px; font-style: italic; color: #333; margin: 15px 0; }
        .details { font-size: 14px; color: #666; margin-top: 50px; }
        .footer { position: absolute; bottom: 40px; width: 80%; text-align: right; }
        .cert-number { font-family: monospace; font-size: 12px; color: #888; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="title">SERTIFIKAT KEHADIRAN</div>
        <div class="subtitle">Diberikan kepada:</div>
        <div class="name">{{ $name }}</div>
        <p>Atas kehadirannya dalam acara:</p>
        <div class="event-title">{{ $event }}</div>
        <div class="details">
            📅 {{ $date }} &nbsp; | &nbsp; 📍 {{ $location }}
        </div>
        <div class="cert-number">Nomor Sertifikat: {{ $cert_number }}</div>
        <div class="footer">
            <p>Ketua Panitia</p>
            <br><br>
            <p>( ................................... )</p>
        </div>
    </div>
</body>
</html>