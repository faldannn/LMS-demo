<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kesalahan Server — {{ config('school.name', config('app.name')) }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height:100vh">
    <div class="container text-center">
        <h1 class="display-4 text-danger">500</h1>
        <p class="lead">Terjadi kesalahan pada server. Silakan coba lagi atau hubungi administrator.</p>
        @if(!empty($message) || isset($exception))
            <div class="alert alert-danger text-start mx-auto mt-3" style="max-width: 800px; font-family: monospace; font-size: 13px; white-space: pre-wrap;">
<strong>Pesan Kesalahan:</strong>
{{ $message ?? (isset($exception) ? $exception->getMessage() : '') }}
@if(isset($exception) && (config('app.debug') || request()->has('debug')))
<hr>
<strong>Lokasi:</strong> {{ $exception->getFile() }}:{{ $exception->getLine() }}
@endif
            </div>
        @endif
        <a href="{{ url('/') }}" class="btn btn-primary mt-3">Kembali ke Dashboard</a>
    </div>
</body>
</html>
