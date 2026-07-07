<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan — {{ config('school.name', config('app.name')) }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height:100vh">
    <div class="container text-center">
        <h1 class="display-4 text-secondary">404</h1>
        <p class="lead">Halaman yang Anda cari tidak ditemukan.</p>
        <a href="{{ url('/') }}" class="btn btn-primary">Kembali ke Dashboard</a>
    </div>
</body>
</html>
