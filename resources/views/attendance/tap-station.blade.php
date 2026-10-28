<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="refresh" content="0;url={{ route('attendance.scan-qr') }}">
    <title>Mengalihkan...</title>
    <script>
        window.location.replace("{{ route('attendance.scan-qr') }}");
    </script>
</head>
<body style="font-family: system-ui, -apple-system, sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; background: #f8fafc; color: #475569;">
    <div style="text-align: center;">
        <p style="font-size: 14px; font-weight: 500;">Fitur Tap Kartu / Alat Pink telah dinonaktifkan.</p>
        <p style="font-size: 12px; color: #94a3b8;">Mengalihkan ke Scanner Presensi...</p>
        <p style="margin-top: 12px;"><a href="{{ route('attendance.scan-qr') }}" style="color: #4f46e5; text-decoration: underline; font-size: 13px;">Klik di sini jika tidak otomatis dialihkan</a></p>
    </div>
</body>
</html>
