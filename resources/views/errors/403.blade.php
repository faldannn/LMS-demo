<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak — {{ config('school.name', config('app.name')) }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height:100vh">
    <div class="container text-center">
        <h1 class="display-4 text-danger">403</h1>
        <p class="lead">{{ $message ?? 'Anda tidak memiliki izin untuk mengakses halaman ini.' }}</p>
        <a href="{{ url('/') }}" class="btn btn-primary">Kembali ke Dashboard</a>
    </div>
</body>
</html>
