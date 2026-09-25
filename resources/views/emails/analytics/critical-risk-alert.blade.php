<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
        }
        .header {
            background-color: #dc3545;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
        }
        .student-item {
            border-left: 4px solid #dc3545;
            padding-left: 15px;
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding-top: 10px;
            padding-bottom: 10px;
        }
        .footer {
            text-align: center;
            font-size: 0.8em;
            color: #777;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>🚨 Peringatan Kritis: Risiko Dropout Santri</h2>
    </div>
    
    <div class="content">
        <p>Assalamu'alaikum Warahmatullahi Wabarakatuh,</p>
        <p>Tim Akademik,</p>
        <p>Sistem Predictive Analytics mendeteksi <strong>{{ $criticalStudents->count() }} santri</strong> berada dalam tingkat risiko <strong>KRITIS</strong> dan memerlukan intervensi segera hari ini.</p>
        
        <h3>Daftar Santri (Kritis):</h3>
        
        @foreach($criticalStudents as $prediction)
            <div class="student-item">
                <strong>Nama Santri:</strong> {{ $prediction->student->full_name ?? 'N/A' }}<br>
                <strong>Skor Risiko:</strong> <span style="color: #dc3545; font-weight: bold;">{{ $prediction->risk_score }} / 100</span><br>
                <strong>Faktor Utama:</strong>
                <ul>
                    @foreach((array)$prediction->risk_factors as $factor)
                        <li>{{ $factor }}</li>
                    @endforeach
                </ul>
            </div>
        @endforeach
        
        <p>
            Harap segera login ke dashboard Admin dan lakukan intervensi melalui fitur WhatsApp 1-Click pada menu Predictive Analytics.
        </p>
        
        <p>
            <a href="{{ route('admin.analytics.predictive.index') }}" style="display: inline-block; padding: 10px 20px; background-color: #0d6efd; color: white; text-decoration: none; border-radius: 5px;">Buka Dashboard Analytics</a>
        </p>
    </div>
    
    <div class="footer">
        <p>&copy; {{ date('Y') }} AL-HIKMAH LMS. All rights reserved.</p>
        <p>Pesan ini dihasilkan secara otomatis oleh sistem Early Warning AL-HIKMAH.</p>
    </div>
</body>
</html>
