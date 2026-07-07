<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terlalu Banyak Permintaan — {{ config('school.name', config('app.name')) }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height:100vh">
    <div class="container text-center">
        <h1 class="display-4 text-warning">429</h1>
        <p class="lead">{{ $message ?? 'Terlalu banyak permintaan. Silakan coba lagi nanti.' }}</p>
        <a href="{{ url('/') }}" class="btn btn-primary mt-3">Kembali ke Dashboard</a>
    </div>
</body>
</html>
